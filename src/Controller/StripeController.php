<?php
namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class StripeController extends AbstractController
{
private EntityManagerInterface $entityManager;
private MailerInterface $mailer;

public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
{
$this->entityManager = $entityManager;
$this->mailer = $mailer;
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

$formation = $this->entityManager->getRepository(Formation::class)->find($formationId);
if (!$formation) {
return new JsonResponse(['error' => 'Formation not found'], 404);
}

if ($formation->getCost() != $cost) {
return new JsonResponse(['error' => 'Invalid cost'], 400);
}

$user = $this->getUser();
if (!$user) {
return new JsonResponse(['error' => 'User not logged in'], 401);
}

Stripe::setApiKey('sk_test_51QwwxI2cr5sURYQmig3Y73VaTq9nPUmCyIhrexKuG0L6fgzNCTL6AiZDQHRjgggMVOL0IeUrVllyIfX8Yy9xfamq00MLgOgy7v');

try {
$checkoutSession = StripeSession::create([
'payment_method_types' => ['card'],
'line_items' => [[
'price_data' => [
'currency' => 'eur',
'unit_amount' => $cost * 100,
'product_data' => ['name' => 'Attestation – ' . $title],
],
'quantity' => 1,
]],
'mode' => 'payment',
'success_url' => $this->generateUrl(
'verify_quiz',
['payment' => 'success', 'formationId' => $formationId],
UrlGeneratorInterface::ABSOLUTE_URL
),
'cancel_url' => $this->generateUrl(
'verify_quiz',
['payment' => 'cancel', 'formationId' => $formationId],
UrlGeneratorInterface::ABSOLUTE_URL
),
'customer_email' => $user->getEmail(),
]);
} catch (\Exception $e) {
return new JsonResponse(['error' => 'Error creating checkout session: ' . $e->getMessage()], 500);
}

$payment = new Payment();
$payment->setStripeSessionId($checkoutSession->id);
$payment->setAmount($cost);
$payment->setStatus(false);
$payment->setUser($user);
$payment->setFormation($formation);

$this->entityManager->persist($payment);
$this->entityManager->flush();

return new JsonResponse(['id' => $checkoutSession->id]);
}

#[Route('/check-payment-status/{formationId}', name: 'check_payment_status', methods: ['GET'])]
public function checkPaymentStatus(int $formationId  ): JsonResponse
{
$user = $this->getUser();
if (!$user) {
return new JsonResponse(['paid' => false], 401);
}

$payment = $this->entityManager->getRepository(Payment::class)->findOneBy([
'user' => $user,
'formation' => $formationId,
'status' => true,
]);

return new JsonResponse(['paid' => $payment !== null]);
}

#[Route('/payment-success/{paymentId}', name: 'payment_success', methods: ['GET'])]
public function paymentSuccess(int $paymentId,MailerInterface $mailer): Response
{
    dd('paymentSuccess route hit');
// Récupérer le paiement
$payment = $this->entityManager->getRepository(Payment::class)->find($paymentId);

// Vérifier si le paiement est validé
if ($payment && $payment->getStatus() === true) {
// Envoi de l'email de confirmation
    // Crée l'e-mail
    $email = (new Email())
        ->from('you@example.com')
        ->to('sadok0703@gmail.com') // Adresse à laquelle envoyer l'email
        ->subject('Paiement réussi')
        ->text('Félicitations, votre paiement a été effectué avec succès et vous pouvez maintenant télécharger votre attestation.');

    try {
        // Envoi de l'email
        $mailer->send($email);
        return new Response('E-mail envoyé avec succès');
    } catch (\Exception $e) {
        // Gérer l'erreur si l'envoi échoue
        return new Response('Erreur lors de l\'envoi de l\'e-mail: ' . $e->getMessage());
    }

// Retourner une réponse indiquant que l'email a été envoyé

}


return new Response('Le paiement n\'a pas été trouvé ou n\'est pas validé.', 404);
}
}
