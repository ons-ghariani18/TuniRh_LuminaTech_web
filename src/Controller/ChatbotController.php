<?php

namespace App\Controller;

use App\Service\HuggingFaceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatbotController extends AbstractController
{
    #[Route('/chatbot', name: 'chatbot')]
    public function index(): Response
    {
        return $this->render('chatbot/index.html.twig');
    }

    #[Route('/chatbot/message', name: 'chatbot_message', methods: ['POST'])]
    public function message(Request $request, HuggingFaceService $huggingFaceService): JsonResponse
    {
        $message = $request->request->get('message');
        if (!$message) {
            return new JsonResponse(['error' => 'Message is required'], 400);
        }

        // Normaliser le message pour la comparaison (minuscules, suppression des accents)
        $normalizedMessage = mb_strtolower(trim(preg_replace('/\p{M}/u', '', $message)));

        // Réponses prédéfinies pour les questions fréquentes
        if (str_contains($normalizedMessage, 'comment postuler') || str_contains($normalizedMessage, 'postuler a une offre')) {
            $response = "Bonjour,  
Voici les étapes pour postuler sur TuniRh :  
- Créez un compte et confirmez votre email.  
- Connectez-vous, puis recherchez une offre.  
- Cliquez sur \"Postuler\", téléchargez CV et lettre de motivation.  
- Remplissez le formulaire et envoyez votre candidature.  
Bonne chance !";
        } elseif (str_contains($normalizedMessage, 'qualifications') || str_contains($normalizedMessage, 'suis qualifie') || str_contains($normalizedMessage, 'suis-je qualifie')) {
            $response = "Bonjour,  
Pour vérifier si vous êtes qualifié, comparez vos compétences aux exigences de l’offre. Par exemple, un poste de commercial nécessite une expérience en vente et des compétences relationnelles. Si vous correspondez, postulez directement sur notre site. Besoin d’aide pour une offre spécifique ?";
        } elseif (str_contains($normalizedMessage, 'types de contrats') || str_contains($normalizedMessage, 'quels contrats')) {
            $response = "Bonjour,  
Sur TuniRh, vous pouvez trouver différents types de contrats :  
- CDI (Contrat à Durée Indéterminée)  
- CDD (Contrat à Durée Déterminée)  
- Stages et alternances  
Utilisez les filtres de recherche pour explorer les offres adaptées !";
        } elseif (str_contains($normalizedMessage, 'mettre a jour') || str_contains($normalizedMessage, 'modifier mon cv') || str_contains($normalizedMessage, 'modifier mon profil')) {
            $response = "Bonjour,  
Pour mettre à jour votre CV ou profil :  
- Connectez-vous à votre compte.  
- Allez dans \"Mon Profil\".  
- Modifiez vos informations ou téléchargez un nouveau CV.  
- Enregistrez les changements.  
Cela augmentera vos chances d’être repéré !";
        } elseif (str_contains($normalizedMessage, 'suivre ma candidature') || str_contains($normalizedMessage, 'etat de ma candidature')) {
            $response = "Bonjour,  
Pour suivre l’état de votre candidature :  
- Connectez-vous à votre compte.  
- Allez dans \"Mes Candidatures\".  
- Consultez le statut de chaque candidature (en attente, en cours, etc.).  
Si besoin, contactez le recruteur via la messagerie.";
        } elseif (str_contains($normalizedMessage, 'secteurs') || str_contains($normalizedMessage, 'domaines')) {
            $response = "Bonjour,  
TuniRh couvre plusieurs secteurs :  
- Commerce et vente  
- Informatique et technologie  
- Santé et éducation  
- Hôtellerie et restauration  
Explorez les offres avec les filtres pour trouver votre domaine !";
        } elseif (str_contains($normalizedMessage, 'contacter un recruteur') || str_contains($normalizedMessage, 'parler a un recruteur')) {
            $response = "Bonjour,  
Pour contacter un recruteur :  
- Connectez-vous et allez dans \"Mes Candidatures\".  
- Cliquez sur une candidature.  
- Utilisez la messagerie pour envoyer un message au recruteur.  
Soyez clair et professionnel dans votre message !";
        } elseif (str_contains($normalizedMessage, 'frais') || str_contains($normalizedMessage, 'cout') || str_contains($normalizedMessage, 'payant')) {
            $response = "Bonjour,  
Non, l’utilisation de TuniRh est gratuite pour les candidats ! Vous pouvez créer un compte, postuler et gérer vos candidatures sans frais. Profitez-en pour trouver l’emploi de vos rêves !";
        } elseif (str_contains($normalizedMessage, 'alertes') || str_contains($normalizedMessage, 'notifications') || str_contains($normalizedMessage, 'nouvelle offre')) {
            $response = "Bonjour,  
Pour recevoir des alertes :  
- Connectez-vous à votre compte.  
- Allez dans \"Paramètres\" ou \"Alertes\".  
- Configurez vos préférences (secteur, type de contrat, etc.).  
- Activez les notifications par email.  
Vous serez informé des nouvelles offres !";
        } elseif (str_contains($normalizedMessage, 'mot de passe') || str_contains($normalizedMessage, 'oublie mon mot de passe')) {
            $response = "Bonjour,  
Si vous avez oublié votre mot de passe :  
- Cliquez sur \"Mot de passe oublié ?\" sur la page de connexion.  
- Entrez votre email et suivez les instructions.  
- Vous recevrez un lien pour réinitialiser votre mot de passe.  
Si besoin, contactez le support !";
        } elseif (str_contains($normalizedMessage, 'annuler une candidature') || str_contains($normalizedMessage, 'retirer ma candidature')) {
            $response = "Bonjour,  
Pour annuler une candidature :  
- Connectez-vous et allez dans \"Mes Candidatures\".  
- Sélectionnez la candidature concernée.  
- Cliquez sur \"Annuler\" ou \"Retirer\".  
Notez que cette action est définitive. Soyez sûr de votre choix !";
        } elseif (str_contains($normalizedMessage, 'competences demandees') || str_contains($normalizedMessage, 'quelles competences')) {
            $response = "Bonjour,  
Les compétences les plus demandées sur TuniRh incluent :  
- Compétences relationnelles (communication, négociation)  
- Maîtrise des outils numériques (informatique, réseaux sociaux)  
- Gestion de projet et organisation  
Consultez les offres pour voir les exigences spécifiques !";
        } elseif (str_contains($normalizedMessage, 'postuler a plusieurs offres') || str_contains($normalizedMessage, 'plusieurs candidatures')) {
            $response = "Bonjour,  
Oui, vous pouvez postuler à plusieurs offres !  
- Assurez-vous que chaque candidature est adaptée à l’offre.  
- Vérifiez que votre CV et lettre de motivation sont pertinents.  
- Suivez vos candidatures dans \"Mes Candidatures\".  
Bonne chance !";
        } elseif (str_contains($normalizedMessage, 'combien de temps') || str_contains($normalizedMessage, 'delai reponse') || str_contains($normalizedMessage, 'quand reponse')) {
            $response = "Bonjour,  
Le délai de réponse varie selon l’employeur. En général :  
- 1 à 2 semaines pour une première réponse.  
- Suivez l’état dans \"Mes Candidatures\".  
Si cela prend trop de temps, envoyez un message au recruteur via la messagerie.";
        } elseif (str_contains($normalizedMessage, 'ameliorer mes chances') || str_contains($normalizedMessage, 'plus de chances') || str_contains($normalizedMessage, 'comment etre selectionne')) {
            $response = "Bonjour,  
Pour améliorer vos chances :  
- Adaptez votre CV et lettre de motivation à chaque offre.  
- Mettez en avant vos compétences clés.  
- Complétez votre profil TuniRh avec des détails pertinents.  
- Soyez proactif : contactez les recruteurs après avoir postulé.  
Bonne chance !";
        } else {
            // Si la question n'est pas fréquente, utiliser Hugging Face
            $response = $huggingFaceService->getResponse($message);
        }

        return new JsonResponse(['response' => $response]);
    }
}