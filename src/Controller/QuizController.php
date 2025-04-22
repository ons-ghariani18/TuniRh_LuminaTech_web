<?php
// src/Controller/QuizController.php
namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Quiz;
use App\Service\GoogleGeminiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuizController extends AbstractController
{
    private $geminiService;
    private $entityManager;

    public function __construct(GoogleGeminiService $geminiService, EntityManagerInterface $entityManager)
    {
        $this->geminiService = $geminiService;
        $this->entityManager = $entityManager;
    }

    #[Route('/generate-quiz/{id}', name: 'generate_quiz_by_id')]
    public function generateQuizByFormation(Formation $formation, Request $request): Response
    {
        $title = $formation->getTitle();
        $error = null;
        $quizData = null;
        $rawResponse = null;
        $debugInfo = null;
        
        try {
            // Generate the quiz questions using Gemini
            $questions = $this->geminiService->generateText($title, 5);
            
            // If questions are generated successfully
            if (!empty($questions)) {
                $quizData = [
                    'title' => $title,
                    'questions' => $questions,
                    'formationId' => $formation->getId()
                ];
                
                // Save quiz to session for verification
                $request->getSession()->set('quiz_data', $quizData);
            } else {
                $error = 'Unable to generate quiz questions at this time. The API may not have returned data in the expected format.';
                // Check if we can find the debug file
                if (file_exists('gemini_response.txt')) {
                    $debugInfo = file_get_contents('gemini_response.txt');
                }
            }
        } catch (\Exception $e) {
            $error = 'Error generating quiz: ' . $e->getMessage();
            // Check if we can find the debug file
            if (file_exists('gemini_response.txt')) {
                $debugInfo = file_get_contents('gemini_response.txt');
            }
        }

        // Render the quiz template
        return $this->render('quiz/generate_quiz.html.twig', [
            'title' => $title,
            'questions' => $questions ?? [],
            'error' => $error,
            'debugInfo' => $debugInfo,
            'formationId' => $formation->getId()
        ]);
    }
    
    #[Route('/verify-quiz', name: 'verify_quiz', methods: ['POST', 'GET'])]
    public function verifyQuiz(Request $request): Response
    {
        // Check if this is a redirect from payment success
        if ($request->isMethod('GET') && $request->query->get('payment') === 'success') {
            $formationId = $request->query->get('formationId');
            if (!$formationId) {
                $this->addFlash('error', 'Formation ID is missing.');
                return $this->redirectToRoute('app_formation_index');
            }
            
            // Get formation details
            $formation = $this->entityManager->getRepository(Formation::class)->find($formationId);
            if (!$formation) {
                $this->addFlash('error', 'Formation not found.');
                return $this->redirectToRoute('app_formation_index');
            }
            
            // Get quiz results from session
            $quizResults = $request->getSession()->get('quiz_results');
            $quizData = $request->getSession()->get('quiz_data');
            
            if (!$quizData || !$quizResults) {
                $this->addFlash('error', 'Quiz data not found. Please try again.');
                return $this->redirectToRoute('app_formation_index');
            }
            
            // Check if user is authenticated
            $user = $this->getUser();
            if (!$user) {
                $this->addFlash('error', 'You must be logged in to access this page.');
                return $this->redirectToRoute('app_login');
            }
            
            // Get user name
            $userName = $user->getFullName() ?? $user->getUserIdentifier();
            
            // Check if formation is paid
            $isPaid = $formation->getCost() > 0;
            
            // Render results template
            return $this->render('quiz/results.html.twig', [
                'title' => $quizData['title'],
                'results' => $quizResults['results'],
                'score' => $quizResults['score'],
                'formationId' => $formationId,
                'userName' => $userName,
                'isPaid' => $isPaid,
                'formationCost' => $formation->getCost(),
                'paymentSuccess' => true
            ]);
        }
        
        // Original code for POST method
        // Get quiz data from session
        $quizData = $request->getSession()->get('quiz_data');
        
        if (!$quizData) {
            $this->addFlash('error', 'No quiz data found in session. Please try again.');
            return $this->redirectToRoute('app_formation_index');
        }
        
        // Get formation to check if it's paid
        $formationId = $quizData['formationId'];
        $formation = $this->entityManager->getRepository(Formation::class)->find($formationId);
        
        if (!$formation) {
            $this->addFlash('error', 'Formation not found.');
            return $this->redirectToRoute('app_formation_index');
        }
        
        $isPaid = $formation->getCost() > 0;
        
        // Get user answers from form submission - more robust way
        $formData = $request->request->all();
        $userAnswers = [];
        
        if (isset($formData['answers']) && is_array($formData['answers'])) {
            $userAnswers = $formData['answers'];
        }
        
        // Debug logs only in dev environment
        if ($this->getParameter('kernel.environment') === 'dev') {
            error_log('User Answers: ' . json_encode($userAnswers));
            error_log('Quiz Data: ' . json_encode($quizData));
        }
        
        // Calculate results
        $results = [];
        $totalCorrect = 0;
        
        foreach ($quizData['questions'] as $index => $question) {
            $questionId = $index + 1;
            $userAnswer = null;
            
            // Safely check if the user provided an answer for this question
            if (array_key_exists($questionId, $userAnswers)) {
                $userAnswer = $userAnswers[$questionId];
            }
            
            // Ensure both are strings and trimmed for comparison
            $correctAnswer = isset($question['correctAnswer']) ? trim((string)$question['correctAnswer']) : '';
            $userAnswerNormalized = $userAnswer ? trim((string)$userAnswer) : '';
            
            // Debug logs only in dev environment
            if ($this->getParameter('kernel.environment') === 'dev') {
                error_log("Question {$questionId} - User: '{$userAnswerNormalized}' vs Correct: '{$correctAnswer}'");
            }
            
            $isCorrect = ($userAnswerNormalized === $correctAnswer);
            
            if ($isCorrect) {
                $totalCorrect++;
                if ($this->getParameter('kernel.environment') === 'dev') {
                    error_log("Question {$questionId} CORRECT!");
                }
            } else {
                if ($this->getParameter('kernel.environment') === 'dev') {
                    error_log("Question {$questionId} INCORRECT!");
                }
            }
            
            $results[$questionId] = [
                'question' => $question['question'],
                'userAnswer' => $userAnswer,
                'correctAnswer' => $correctAnswer,
                'isCorrect' => $isCorrect,
                'options' => $question['options'] ?? []
            ];
        }
        
        $score = [
            'total' => count($quizData['questions']),
            'correct' => $totalCorrect,
            'percentage' => (count($quizData['questions']) > 0) 
                ? round(($totalCorrect / count($quizData['questions'])) * 100) 
                : 0,
            'passed' => $totalCorrect >= 3 // Pass if 3 or more correct answers
        ];
        
        // Debug logs only in dev environment
        if ($this->getParameter('kernel.environment') === 'dev') {
            error_log('Total Correct: ' . $totalCorrect . ' out of ' . count($quizData['questions']));
        }
        
        // Get current user if authenticated
        $user = $this->getUser();
        $userName = $user ? ($user->getFullName() ?? $user->getUserIdentifier()) : 'Anonymous User';
        
        // Store the results in session for potential reuse
        $request->getSession()->set('quiz_results', [
            'results' => $results,
            'score' => $score,
        ]);
        
        // Render results template
        return $this->render('quiz/results.html.twig', [
            'title' => $quizData['title'],
            'results' => $results,
            'score' => $score, 
            'formationId' => $quizData['formationId'],
            'userName' => $userName,
            'isPaid' => $isPaid,
            'formationCost' => $formation->getCost(),
            'debug' => ($this->getParameter('kernel.environment') === 'dev') ? [
                'userAnswers' => $userAnswers,
                'questions' => $quizData['questions']
            ] : null
        ]);
    }
    
    #[Route('/generate-attestation/{formationId}', name: 'generate_attestation')]
    public function generateAttestation(Request $request, int $formationId): Response
    {
        // Get formation details
        $formation = $this->entityManager->getRepository(Formation::class)->find($formationId);
        
        if (!$formation) {
            $this->addFlash('error', 'Formation not found');
            return $this->redirectToRoute('app_formation_index');
        }
        
        // Get current user if authenticated
        $user = $this->getUser();
        $userName = $user ? $user->getFullName() ?? $user->getUserIdentifier() : 'Anonymous User';
        
        // Get quiz data from session to verify they passed
        $quizData = $request->getSession()->get('quiz_data');
        
        if (!$quizData || $quizData['formationId'] !== $formationId) {
            $this->addFlash('error', 'Quiz data not found or does not match the requested formation');
            return $this->redirectToRoute('app_formation_index');
        }
        
        // Generate PDF using DomPDF or another PDF library
        // For now we'll just render a template that could be converted to PDF
        return $this->render('quiz/attestation.html.twig', [
            'formation' => $formation,
            'userName' => $userName,
            'date' => new \DateTime()
        ]);
    }
}
