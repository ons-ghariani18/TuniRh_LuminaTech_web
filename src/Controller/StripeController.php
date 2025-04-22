<?php
// src/Controller/StripeController.php
namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe;
use App\Service\EmailService;

class StripeController extends AbstractController
{
    private $entityManager;
    private $emailService;
    
    public function __construct(EntityManagerInterface $entityManager, EmailService $emailService)
    {
        $this->entityManager = $entityManager;
        $this->emailService = $emailService;
    }

    #[Route('/create-checkout-session', name: 'create_checkout_session', methods: ['POST'])]
    public function createCheckoutSession(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $formationId = $data['formationId'] ?? null;
        $cost = $data['cost'] ?? 0;
        $title = $data['title'] ?? 'Formation';
        
        if (!$formationId) {
            return new JsonResponse(['error' => 'Formation ID is required'], 400);
        }
        
        // Get the formation to verify price
        $formation = $this->entityManager->getRepository(Formation::class)->find($formationId);
        
        if (!$formation) {
            return new JsonResponse(['error' => 'Formation not found'], 404);
        }
        
        // Verify cost
        if ($formation->getCost() != $cost) {
            return new JsonResponse(['error' => 'Invalid cost'], 400);
        }
        
        // Get current user
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not logged in'], 401);
        }
        
        // Check if user has already paid
        $paymentRepository = $this->entityManager->getRepository(Payment::class);
        $alreadyPaid = $paymentRepository->hasUserPaidForFormation($user, $formation);
        
        if ($alreadyPaid) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Already paid',
                'redirectUrl' => $this->generateUrl('verify_quiz', ['payment' => 'success'])
            ]);
        }
        
        \Stripe\Stripe::setApiKey('sk_test_51QwwxI2cr5sURYQmig3Y73VaTq9nPUmCyIhrexKuG0L6fgzNCTL6AiZDQHRjgggMVOL0IeUrVllyIfX8Yy9xfamq00MLgOgy7v');
        
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $cost * 100, // Amount in cents
                    'product_data' => [
                        'name' => 'Attestation for: ' . $title,
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('verify_quiz', ['payment' => 'success', 'formationId' => $formationId], 0),
            'cancel_url' => $this->generateUrl('verify_quiz', ['payment' => 'cancel', 'formationId' => $formationId], 0),
            'customer_email' => $user->getEmail(),
        ]);
        
        // Create a payment record
        $payment = new Payment();
        $payment->setStripeSessionId($checkout_session->id);
        $payment->setAmount($cost);
        $payment->setStatus(false); // Not paid yet
        $payment->setUser($user);
        $payment->setFormation($formation);
        
        $this->entityManager->persist($payment);
        $this->entityManager->flush();
        
        return new JsonResponse(['id' => $checkout_session->id]);
        
        /*
        // For demo purposes, return a simulated session ID
        // In a real app, you would return the actual session ID from Stripe
        return new JsonResponse(['id' => 'demo_session_' . uniqid()]);
        */
    }
    
    #[Route('/check-payment-status/{formationId}', name: 'check_payment_status', methods: ['GET'])]
    public function checkPaymentStatus(int $formationId, Request $request): JsonResponse
    {
        // Get the formation
        $formation = $this->entityManager->getRepository(Formation::class)->find($formationId);
        
        if (!$formation) {
            return new JsonResponse(['error' => 'Formation not found'], 404);
        }
        
        // Get current user
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not logged in'], 401);
        }
        
        // Get request parameter to check if coming from success URL
        $paymentSuccess = $request->query->get('payment') === 'success';
        
        // If coming from success URL, update payment status
        if ($paymentSuccess) {
            $paymentRepository = $this->entityManager->getRepository(Payment::class);
            
            // Find the most recent payment for this user and formation
            $payment = $this->entityManager->createQueryBuilder()
                ->select('p')
                ->from(Payment::class, 'p')
                ->where('p.user = :user')
                ->andWhere('p.formation = :formation')
                ->orderBy('p.createdAt', 'DESC')
                ->setParameter('user', $user)
                ->setParameter('formation', $formation)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
            
            if ($payment) {
                $payment->setStatus(true);
                $this->entityManager->flush();
                
                // Envoyer un email de confirmation
                try {
                    $this->emailService->sendPaymentSuccessEmail($user, $formation, $payment->getAmount());
                } catch (\Exception $e) {
                    // Log l'erreur mais continuer le processus
                    if ($this->getParameter('kernel.environment') === 'dev') {
                        error_log('Error sending success email: ' . $e->getMessage());
                    }
                }
            }
        } else if ($request->query->get('payment') === 'cancel') {
            // Si le paiement a été annulé, envoyer un email d'échec
            try {
                $this->emailService->sendPaymentFailureEmail(
                    $user, 
                    $formation, 
                    $formation->getCost(), 
                    'Le paiement a été annulé'
                );
            } catch (\Exception $e) {
                // Log l'erreur mais continuer le processus
                if ($this->getParameter('kernel.environment') === 'dev') {
                    error_log('Error sending failure email: ' . $e->getMessage());
                }
            }
        }
        
        // Check if user has paid for this formation
        $paymentRepository = $this->entityManager->getRepository(Payment::class);
        $hasPaid = $paymentRepository->hasUserPaidForFormation($user, $formation);
        
        return new JsonResponse(['paid' => $hasPaid]);
    }
    
    #[Route('/webhook/stripe', name: 'stripe_webhook', methods: ['POST'])]
    public function handleStripeWebhook(Request $request): Response
    {
        \Stripe\Stripe::setApiKey('sk_test_51QwwxI2cr5sURYQmig3Y73VaTq9nPUmCyIhrexKuG0L6fgzNCTL6AiZDQHRjgggMVOL0IeUrVllyIfX8Yy9xfamq00MLgOgy7v');
        
        $payload = $request->getContent();
        $sig_header = $request->headers->get('stripe-signature');
        $endpoint_secret = null; // À remplacer par votre webhook signing secret en production
        
        try {
            // En environnement de production, vérifiez la signature du webhook
            /*
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
            */
            
            // Pour l'environnement de test, nous allons simplement décoder le payload
            $data = json_decode($payload, true);
            $eventType = $data['type'] ?? '';
            
            // Traiter l'événement
            if ($eventType === 'checkout.session.completed') {
                $session = $data['data']['object'];
                $sessionId = $session['id'] ?? null;
                
                if ($sessionId) {
                    // Rechercher le paiement correspondant à cette session
                    $payment = $this->entityManager->getRepository(Payment::class)->findByStripeSessionId($sessionId);
                    
                    if ($payment) {
                        $user = $payment->getUser();
                        $formation = $payment->getFormation();
                        
                        // Mettre à jour le statut du paiement
                        $payment->setStatus(true);
                        $payment->setPaymentIntent($session['payment_intent'] ?? null);
                        $this->entityManager->flush();
                        
                        // Envoyer un email de confirmation
                        try {
                            $this->emailService->sendPaymentSuccessEmail($user, $formation, $payment->getAmount());
                        } catch (\Exception $e) {
                            // Log l'erreur mais continuer le processus
                            if ($this->getParameter('kernel.environment') === 'dev') {
                                error_log('Error sending success email: ' . $e->getMessage());
                            }
                        }
                        
                        // Enregistrer des logs en développement
                        if ($this->getParameter('kernel.environment') === 'dev') {
                            error_log('Payment updated: ' . $sessionId);
                        }
                    }
                }
            } else if ($eventType === 'checkout.session.expired' || $eventType === 'payment_intent.payment_failed') {
                // Cas d'échec ou d'expiration
                $session = $data['data']['object'];
                $sessionId = $session['id'] ?? null;
                
                if ($sessionId) {
                    // Rechercher le paiement correspondant à cette session
                    $payment = $this->entityManager->getRepository(Payment::class)->findByStripeSessionId($sessionId);
                    
                    if ($payment) {
                        $user = $payment->getUser();
                        $formation = $payment->getFormation();
                        
                        // Envoyer un email d'échec
                        try {
                            $reason = '';
                            if ($eventType === 'checkout.session.expired') {
                                $reason = 'La session de paiement a expiré';
                            } else {
                                $reason = 'Le paiement a échoué';
                            }
                            
                            $this->emailService->sendPaymentFailureEmail($user, $formation, $payment->getAmount(), $reason);
                        } catch (\Exception $e) {
                            // Log l'erreur mais continuer le processus
                            if ($this->getParameter('kernel.environment') === 'dev') {
                                error_log('Error sending failure email: ' . $e->getMessage());
                            }
                        }
                        
                        // Enregistrer des logs en développement
                        if ($this->getParameter('kernel.environment') === 'dev') {
                            error_log('Payment failed: ' . $sessionId);
                        }
                    }
                }
            }
            
            return new Response('Webhook processed successfully', 200);
        } catch (\Exception $e) {
            // Enregistrer l'erreur dans les logs
            if ($this->getParameter('kernel.environment') === 'dev') {
                error_log('Stripe webhook error: ' . $e->getMessage());
            }
            
            return new Response('Webhook error: ' . $e->getMessage(), 400);
        }
    }
} 