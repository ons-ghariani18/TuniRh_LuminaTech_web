<?php

namespace App\Controller;

use App\Service\HuggingFaceService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ChatbotController extends AbstractController
{
    private $urlGenerator;
    private $logger;

    public function __construct(UrlGeneratorInterface $urlGenerator, LoggerInterface $logger)
    {
        $this->urlGenerator = $urlGenerator;
        $this->logger = $logger;
    }

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
            $this->logger->error('Chatbot: Message is required');
            return new JsonResponse(['error' => 'Message is required'], 400);
        }

        $this->logger->info('Chatbot: Received message', ['message' => $message]);

        // Normaliser le message pour la comparaison
        $normalizedMessage = mb_strtolower(trim(preg_replace('/\p{M}/u', '', $message)));

        // Réponses prédéfinies sous forme de tableau associatif
        $predefinedResponses = [
            'comment postuler|postuler a une offre' => "Bonjour,  
Voici les étapes pour postuler sur TuniRh :  
- Créez un compte et confirmez votre email.  
- Connectez-vous, puis recherchez une offre.  
- Cliquez sur \"Postuler\", téléchargez CV et lettre de motivation.  
- Remplissez le formulaire et envoyez votre candidature.  
Bonne chance !",

            'qualifications|suis qualifie|suis-je qualifie' => "Bonjour,  
Pour vérifier si vous êtes qualifié, comparez vos compétences aux exigences de l’offre. Par exemple, un poste de commercial nécessite une expérience en vente et des compétences relationnelles. Si vous correspondez, postulez directement sur notre site. Besoin d’aide pour une offre spécifique ?",

            'offres disponibles|quelles offres|offres dispo' => sprintf("Bonjour,  
Consultez les offres disponibles sur TuniRh en visitant notre page dédiée :  
<a href=\"%s\" target=\"_blank\">Voir les offres</a>.  
Utilisez les filtres pour trouver celles qui vous correspondent !", 
                $this->urlGenerator->generate('app_offres_index', [], UrlGeneratorInterface::ABSOLUTE_URL)),

            'types de contrats|quels contrats' => "Bonjour,  
Sur TuniRh, vous pouvez trouver différents types de contrats :  
- CDI (Contrat à Durée Indéterminée)  
- CDD (Contrat à Durée Déterminée)  
- Stages et alternances  
Utilisez les filtres de recherche pour explorer les offres adaptées !",

            'mettre a jour|modifier mon cv|modifier mon profil' => "Bonjour,  
Pour mettre à jour votre CV ou profil :  
- Connectez-vous à votre compte.  
- Allez dans \"Mon Profil\".  
- Modifiez vos informations ou téléchargez un nouveau CV.  
- Enregistrez les changements.  
Cela augmentera vos chances d’être repéré !",

            'suivre ma candidature|etat de ma candidature' => "Bonjour,  
Pour suivre l’état de votre candidature :  
- Connectez-vous à votre compte.  
- Allez dans \"Mes Candidatures\".  
- Consultez le statut de chaque candidature (en attente, en cours, etc.).  
Si besoin, contactez le recruteur via la messagerie.",

            'secteurs|domaines' => "Bonjour,  
TuniRh couvre plusieurs secteurs :  
- Commerce et vente  
- Informatique et technologie  
- Santé et éducation  
- Hôtellerie et restauration  
Explorez les offres avec les filtres pour trouver votre domaine !",

            'contacter un recruteur|parler a un recruteur' => "Bonjour,  
Pour contacter un recruteur :  
- Connectez-vous et allez dans \"Mes Candidatures\".  
- Cliquez sur une candidature.  
- Utilisez la messagerie pour envoyer un message au recruteur.  
Soyez clair et professionnel dans votre message !",

            'frais|cout|payant' => "Bonjour,  
Non, l’utilisation de TuniRh est gratuite pour les candidats ! Vous pouvez créer un compte, postuler et gérer vos candidatures sans frais. Profitez-en pour trouver l’emploi de vos rêves !",

            'alertes|notifications|nouvelle offre' => "Bonjour,  
Pour recevoir des alertes :  
- Connectez-vous à votre compte.  
- Allez dans \"Paramètres\" ou \"Alertes\".  
- Configurez vos préférences (secteur, type de contrat, etc.).  
- Activez les notifications par email.  
Vous serez informé des nouvelles offres !",

            'mot de passe|oublie mon mot de passe' => "Bonjour,  
Si vous avez oublié votre mot de passe :  
- Cliquez sur \"Mot de passe oublié ?\" sur la page de connexion.  
- Entrez votre email et suivez les instructions.  
- Vous recevrez un lien pour réinitialiser votre mot de passe.  
Si besoin, contactez le support !",

            'annuler une candidature|retirer ma candidature' => "Bonjour,  
Pour annuler une candidature :  
- Connectez-vous et allez dans \"Mes Candidatures\".  
- Sélectionnez la candidature concernée.  
- Cliquez sur \"Annuler\" ou \"Retirer\".  
Notez que cette action est définitive. Soyez sûr de votre choix !",

            'competences demandees|quelles competences' => "Bonjour,  
Les compétences les plus demandées sur TuniRh incluent :  
- Compétences relationnelles (communication, négociation)  
- Maîtrise des outils numériques (informatique, réseaux sociaux)  
- Gestion de projet et organisation  
Consultez les offres pour voir les exigences spécifiques !",

            'postuler a plusieurs offres|plusieurs candidatures' => "Bonjour,  
Oui, vous pouvez postuler à plusieurs offres !  
- Assurez-vous que chaque candidature est adaptée à l’offre.  
- Vérifiez que votre CV et lettre de motivation sont pertinents.  
- Suivez vos candidatures dans \"Mes Candidatures\".  
Bonne chance !",

            'combien de temps|delai reponse|quand reponse' => "Bonjour,  
Le délai de réponse varie selon l’employeur. En général :  
- 1 à 2 semaines pour une première réponse.  
- Suivez l’état dans \"Mes Candidatures\".  
Si cela prend trop de temps, envoyez un message au recruteur via la messagerie.",

            'ameliorer mes chances|plus de chances|comment etre selectionne' => "Bonjour,  
Pour améliorer vos chances :  
- Adaptez votre CV et lettre de motivation à chaque offre.  
- Mettez en avant vos compétences clés.  
- Complétez votre profil TuniRh avec des détails pertinents.  
- Soyez proactif : contactez les recruteurs après avoir postulé.  
Bonne chance !",

            'preparer entretien|comment entretien|entretien d\'embauche' => "Bonjour,  
Pour préparer un entretien :  
- Renseignez-vous sur l’entreprise et le poste.  
- Préparez des réponses aux questions courantes (parlez de vos forces, expériences).  
- Pratiquez avec un ami ou devant un miroir.  
- Soyez ponctuel et habillez-vous de manière professionnelle.  
Bonne chance !",

            'documents necessaires|quels documents|documents pour postuler' => "Bonjour,  
Pour postuler sur TuniRh, vous aurez besoin :  
- Un CV à jour (format PDF recommandé).  
- Une lettre de motivation adaptée à l’offre.  
- Éventuellement, des certificats ou références si demandés.  
Assurez-vous que vos documents sont bien lisibles !",
        ];

        // Vérifier si le message correspond à une question prédéfinie
        $response = null;
        foreach ($predefinedResponses as $pattern => $predefinedResponse) {
            $patterns = explode('|', $pattern);
            foreach ($patterns as $p) {
                if (str_contains($normalizedMessage, $p)) {
                    $response = $predefinedResponse;
                    break 2;
                }
            }
        }

        // Si aucune réponse prédéfinie, utiliser Hugging Face
        if ($response === null) {
            $response = $huggingFaceService->getResponse($message);
        }

        $this->logger->info('Chatbot: Generated response', ['response' => $response]);

        return new JsonResponse(['response' => $response]);
    }
}