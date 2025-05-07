<?php
namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Quiz;
use App\Service\GoogleGeminiService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;


class QuizController extends AbstractController
{
    private GoogleGeminiService $geminiService;


    private EntityManagerInterface $entityManager;

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
    public function verifyQuiz(Request $request,MailerInterface $mailer): Response
    {
// Check if this is a redirect from payment success
        if ($request->isMethod('GET') && $request->query->get('payment') === 'success') {
            $formationId = $request->query->get('formationId');
            $email = (new TemplatedEmail())
                ->from(new Address('no-reply@monentreprise.com', 'TUNIRH'))
                ->to(new Address($this->getUser()->getEmail()))
                ->subject('Confirmation de votre paiement')
                // on va utiliser un template Twig
                ->htmlTemplate('emails/payment.html.twig')
                ->context([
                    'clientName' => $this->getUser()->getFullName() ,
                    'amount'     => $this->entityManager->getRepository(Formation::class)->find($formationId)->getCost(),

                ]);
            // Embed logo et signature inline
            $projectDir = $this->getParameter('kernel.project_dir');

            $email
                ->embedFromPath($projectDir.'/public/img/logo.jpg', 'logo_cid')
                ->embedFromPath($projectDir.'/public/img/signature.png', 'sig_cid')
            ;

            try {
                // Envoi de l'email
                $mailer->send($email);

            } catch (\Exception $e) {
                // Gérer l'erreur si l'envoi échoue
                return new Response('Erreur lors de l\'envoi de l\'e-mail: ' . $e->getMessage());
            }

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

// Original code for POST method (handling quiz results)
        $quizData = $request->getSession()->get('quiz_data');

        if (!$quizData) {
            $this->addFlash('error', 'No quiz data found in session. Please try again.');
            return $this->redirectToRoute('app_formation_index');
        }

        $formationId = $quizData['formationId'];
        $formation = $this->entityManager->getRepository(Formation::class)->find($formationId);

        if (!$formation) {
            $this->addFlash('error', 'Formation not found.');
            return $this->redirectToRoute('app_formation_index');
        }

        $isPaid = $formation->getCost() > 0;

// Get user answers from form submission
        $formData = $request->request->all();
        $userAnswers = $formData['answers'] ?? [];

// Calculate results
        $results = [];
        $totalCorrect = 0;

        foreach ($quizData['questions'] as $index => $question) {
            $questionId = $index + 1;
            $userAnswer = $userAnswers[$questionId] ?? null;
            $correctAnswer = trim((string)$question['correctAnswer']);
            $userAnswerNormalized = $userAnswer ? trim((string)$userAnswer) : '';

            $isCorrect = ($userAnswerNormalized === $correctAnswer);

            if ($isCorrect) {
                $totalCorrect++;
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

        $user = $this->getUser();
        $userName = $user ? ($user->getFullName() ?? $user->getUserIdentifier()) : 'Anonymous User';

// Store the results in session
        $request->getSession()->set('quiz_results', [
            'results' => $results,
            'score' => $score,
        ]);


        return $this->render('quiz/results.html.twig', [
            'title' => $quizData['title'],
            'results' => $results,
            'score' => $score,
            'formationId' => $quizData['formationId'],
            'userName' => $userName,
            'isPaid' => $isPaid,
            'formationCost' => $formation->getCost(),
        ]);
    }


    #[Route('/generate-attestation/{formationId}', name: 'generate_attestation')]
    public function generateAttestation(
        Request $request,
        Pdf $snappyPdf,
        int $formationId
    ): Response {
        // 1) Récupération de la formation
        $formation = $this->entityManager->getRepository(Formation::class)->find($formationId);
        if (!$formation) {
            $this->addFlash('error', 'Formation introuvable.');
            return $this->redirectToRoute('app_formation_index');
        }

        // 2) Préparation des données utilisateur
        $user = $this->getUser();
        $userName = $user
            ? ($user->getFullName() ?? $user->getUserIdentifier())
            : 'Utilisateur anonyme';

        // 3) (Optionnel) Vérifier le quiz en session
        $quizData = $request->getSession()->get('quiz_data');
        if (!$quizData || $quizData['formationId'] !== $formationId) {
            $this->addFlash('error', 'Données de quiz manquantes ou invalides.');
            return $this->redirectToRoute('app_formation_index');
        }

        // 4) Rendu HTML de la vue
        $html = $this->renderView('quiz/attestation.html.twig', [
            'formation' => $formation,
            'userName'  => $userName,
            'date'      => new \DateTime(),
        ]);

        // 5) Génération du PDF
        $pdfContent = $snappyPdf->getOutputFromHtml(
            $html,
            [
                // ici vous pouvez ajouter des options WKHTMLTOPDF :
                'page-size'   => 'A4',
                'orientation' => 'Landscape',
                'margin-top'  => '10mm',
            ]
        );

        // 6) Retour de la réponse PDF
        return new Response($pdfContent, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"attestation-{$formationId}.pdf\"",
        ]);
    }
}
