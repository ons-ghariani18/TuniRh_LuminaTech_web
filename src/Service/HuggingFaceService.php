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
            $prompt = "Tu es un assistant RH pour TuniRh, une plateforme de recrutement. Réponds aux questions sur les candidatures, les offres d'emploi, et les processus RH de manière professionnelle, concise (maximum 100 mots), chaleureuse, et structurée (utilise des listes à puces pour les étapes si nécessaire). Commence toujours par saluer l'utilisateur (par exemple, 'Bonjour' ou 'Bonsoir'). Voici des exemples :  
- Question : 'Comment postuler à une offre ?' Réponse : Bonjour, Voici les étapes pour postuler sur TuniRh : - Créez un compte et confirmez votre email. - Connectez-vous, puis recherchez une offre. - Cliquez sur \"Postuler\", téléchargez CV et lettre de motivation. - Remplissez le formulaire et envoyez votre candidature. Bonne chance !  
- Question : 'Suis-je qualifié pour un poste ?' Réponse : Bonjour, Pour vérifier si vous êtes qualifié, comparez vos compétences aux exigences de l’offre. Par exemple, un poste de commercial nécessite une expérience en vente et des compétences relationnelles. Si vous correspondez, postulez directement sur notre site. Besoin d’aide pour une offre spécifique ?  
Question : " . $message;

            $response = $this->httpClient->request('POST', 'https://api-inference.huggingface.co/models/' . $this->model, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'inputs' => $prompt,
                    'parameters' => [
                        'max_length' => 100,
                        'temperature' => 0.5,
                        'top_p' => 0.95,
                        'num_return_sequences' => 1,
                    ],
                ],
            ]);

            $data = $response->toArray();
            $this->logger->info('Hugging Face response', ['response' => $data]);

            $generatedText = $data[0]['generated_text'] ?? 'Désolé, je n\'ai pas compris.';
            $cleanedResponse = str_replace($prompt, '', $generatedText);
            return trim($cleanedResponse);
        } catch (\Exception $e) {
            $this->logger->error('Hugging Face API error', ['error' => $e->getMessage()]);
            return 'Erreur : Impossible de répondre pour le moment. Veuillez réessayer plus tard.';
        }
    }

    public function extractSkills(string $text): array
    {
        try {
            $prompt = "Tu es un extracteur de compétences pour TuniRh. À partir du texte suivant, extrais uniquement une liste de compétences techniques et comportementales (par exemple, PHP, communication, gestion de projet). Ignore les autres informations et retourne les compétences sous forme de liste, sans texte supplémentaire. Texte : " . $text;

            $response = $this->httpClient->request('POST', 'https://api-inference.huggingface.co/models/' . $this->model, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'inputs' => $prompt,
                    'parameters' => [
                        'max_length' => 200,
                        'temperature' => 0.3,
                        'top_p' => 0.9,
                        'num_return_sequences' => 1,
                    ],
                ],
            ]);

            $data = $response->toArray();
            $this->logger->info('Hugging Face skills extraction', ['response' => $data]);

            $generatedText = $data[0]['generated_text'] ?? '';
            $cleanedText = str_replace($prompt, '', $generatedText);
            $skills = array_filter(array_map('trim', explode("\n", $cleanedText)));

            return $skills;
        } catch (\Exception $e) {
            $this->logger->error('Hugging Face API skills extraction error', ['error' => $e->getMessage()]);
            return [];
        }
    }
}