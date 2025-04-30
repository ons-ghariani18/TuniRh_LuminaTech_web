<?php
// src/Service/GoogleGeminiService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\Exception\TransportExceptionInterface;
use RuntimeException;

class GoogleGeminiService
{
    private HttpClientInterface $httpClient;
    private string $apiKey;

    private const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-pro-001:generateContent';

    public function __construct(HttpClientInterface $httpClient, string $googleApiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $googleApiKey;
    }

    public function generateText(string $prompt, int $numQuestions): array
    {
        try {
            // Make the prompt more explicit with examples
            $enhancedPrompt = "Create a quiz with exactly 5 questions about '{$prompt}'. 

For each question, follow this EXACT format:
Question: What is the question?
a) First option
b) Second option
c) Third option
d) Fourth option
Correct: a

Example output:
Question: What is the capital of France?
a) Paris
b) London
c) Berlin
d) Madrid
Correct: a

Question: Who wrote 'Romeo and Juliet'?
a) Charles Dickens
b) Jane Austen
c) William Shakespeare
d) Mark Twain
Correct: c

Now create 5 questions about '{$prompt}' following this exact format:";

            $response = $this->httpClient->request(
                'POST',
                sprintf('%s?key=%s', self::GEMINI_API_URL, $this->apiKey),
                [
                    'json' => [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $enhancedPrompt],
                                ],
                            ],
                        ],
                        'generationConfig' => [
                            'temperature' => 0.2,  // Lower temperature for more deterministic output
                            'topP' => 0.8,
                            'topK' => 40,
                            'maxOutputTokens' => 1024
                        ]
                    ],
                ]
            );

            $data = $response->toArray(false);

            // Check if we have valid data structure
            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                // Log the response for debugging
                error_log('Gemini API response invalid structure: ' . json_encode($data));
                throw new RuntimeException('Invalid response structure from Gemini API');
            }

            // Retrieve the generated text
            $raw = $data['candidates'][0]['content']['parts'][0]['text'];
            
            // Log the raw text for debugging
            error_log('Gemini raw response: ' . $raw);
            
            // Debug output - dump the raw response
            file_put_contents('gemini_response.txt', $raw);
            
            // Extract questions using a more robust approach with regex pattern matching
            $pattern = '/Question:\s*(.*?)\s*\n\s*a\)\s*(.*?)\s*\n\s*b\)\s*(.*?)\s*\n\s*c\)\s*(.*?)\s*\n\s*d\)\s*(.*?)\s*\n\s*Correct:\s*([a-d])/is';
            
            preg_match_all($pattern, $raw, $matches, PREG_SET_ORDER);
            
            $questions = [];
            
            if (!empty($matches)) {
                foreach ($matches as $match) {
                    // Make sure correct answer is properly formatted and lowercase
                    $correctAnswer = strtolower(trim($match[6]));
                    
                    $question = [
                        'question' => trim($match[1]),
                        'options' => [
                            'a' => trim($match[2]),
                            'b' => trim($match[3]),
                            'c' => trim($match[4]),
                            'd' => trim($match[5]),
                        ],
                        'correctAnswer' => $correctAnswer,
                    ];
                    $questions[] = $question;
                    
                    // Debug log
                    error_log("Parsed question with correctAnswer: {$correctAnswer}");
                }
            } else {
                // Try alternative pattern for different formatting
                $questions = $this->parseQuestionsManually($raw);
            }

            // If we couldn't parse any questions, throw an exception
            if (empty($questions)) {
                error_log('Failed to parse questions from response: ' . $raw);
                throw new RuntimeException('Failed to parse questions from Gemini response');
            }

            return $questions;
        } catch (RuntimeException $e) {
            // Log the specific error
            error_log('Gemini API RuntimeException: ' . $e->getMessage());
            throw $e;
        } catch (\Throwable $e) {
            // Log any other error
            error_log('Gemini API error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Manual parsing approach for when regex fails
     */
    private function parseQuestionsManually(string $raw): array
    {
        $lines = array_filter(array_map('trim', explode("\n", $raw)));
        $questions = [];
        $current = null;
        $correctAnswer = null;
        
        foreach ($lines as $line) {
            // Detect question lines with multiple different formats
            if (preg_match('/^(?:Question\s*\d*:|Q\d+:|Q\d+\.\s+|\d+\.\s+)?\s*(.+\?)$/i', $line, $m)) {
                if ($current && isset($current['question']) && !empty($current['options'])) {
                    if ($correctAnswer) {
                        $current['correctAnswer'] = $correctAnswer;
                        error_log("Manual parsing - Adding question with correctAnswer: {$correctAnswer}");
                    }
                    $questions[] = $current;
                }
                $current = [
                    'question' => $m[1],
                    'options'  => [],
                    'correctAnswer' => null,
                ];
                $correctAnswer = null;
            }
            // Detect option lines with various formats (a), a., A), A., etc.)
            elseif ($current && preg_match('/^([a-d])[.):]\s*(.+)$/i', $line, $m)) {
                $optionLetter = strtolower($m[1]);
                $optionText = $m[2];
                $current['options'][$optionLetter] = $optionText;
                
                // Debug to see what options are being added
                error_log("Added option {$optionLetter}: {$optionText}");
            }
            // Detect correct answer with various formats
            elseif ($current && preg_match('/^(?:Correct(?:\s*answer)?:|Answer:)\s*([a-d])$/i', $line, $m)) {
                $correctAnswer = strtolower(trim($m[1]));
                $current['correctAnswer'] = $correctAnswer;
                error_log("Found correct answer marker: {$correctAnswer}");
            }
            // Find correct answer if it's mentioned in text elsewhere
            elseif ($current && preg_match('/correct\s+(?:answer|option)(?:\s+is)?(?:\s*:)?\s*(?:option\s*)?([a-d])/i', $line, $m)) {
                $correctAnswer = strtolower(trim($m[1]));
                $current['correctAnswer'] = $correctAnswer;
                error_log("Found embedded correct answer: {$correctAnswer}");
            }
        }
        
        // Add the last question if it exists
        if ($current && isset($current['question']) && !empty($current['options'])) {
            if ($correctAnswer) {
                $current['correctAnswer'] = $correctAnswer;
                error_log("Manual parsing - Adding final question with correctAnswer: {$correctAnswer}");
            }
            $questions[] = $current;
        }
        
        // Ensure all questions have a correct answer, defaulting to 'a' if missing
        foreach ($questions as $index => $question) {
            if (empty($question['correctAnswer']) && !empty($question['options']) && isset($question['options']['a'])) {
                $questions[$index]['correctAnswer'] = 'a';
                error_log("Adding default correctAnswer 'a' to question {$index}");
            }
        }
        
        return $questions;
    }
}
