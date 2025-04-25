<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HuggingFaceService
{
    private $httpClient;
    private $apiKey;
    private $model;
    private $logger;

    public function __construct(HttpClientInterface $httpClient, string $apiKey, string $model, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->logger = $logger;
    }

    public function getResponse(string $message): string
    {
        try {
            $prompt = "Tu es un assistant RH pour TuniRh, une plateforme de recrutement. Réponds aux questions sur les candidatures, les offres d'emploi, et les processus RH de manière professionnelle, concise (maximum 100 mots), chaleureuse, et structurée (utilise des listes à puces pour les étapes si nécessaire). Commence toujours par saluer l'utilisateur (par exemple, 'Bonjour' ou 'Bonsoir'). Question : " . $message;

            $response = $this->httpClient->request('POST', 'https://api-inference.huggingface.co/models/' . $this->model, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'inputs' => $prompt,
                    'parameters' => [
                        'max_length' => 100, // Réduire encore plus pour des réponses très concises
                        'temperature' => 0.5,
                        'top_p' => 0.95,
                        'num_return_sequences' => 1,
                    ],
                ],
            ]);

            $data = $response->toArray();
            $this->logger->info('Hugging Face response', ['response' => $data]);

            $generatedText = $data[0]['generated_text'] ?? 'Désolé, je n\'ai pas compris.';
            // Supprimer le prompt de la réponse
            $cleanedResponse = str_replace($prompt, '', $generatedText);
            return trim($cleanedResponse);
        } catch (\Exception $e) {
            $this->logger->error('Hugging Face API error', ['error' => $e->getMessage()]);
            return 'Erreur : Impossible de répondre pour le moment. Veuillez réessayer plus tard.';
        }
    }
}