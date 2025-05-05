<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\SignUpType;
use App\Form\LoginType;
use App\Form\ForgotPasswordType;
use App\Form\VerifyCodeType;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\SmsService;
use App\Service\NotificationService;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberType;
use Psr\Log\LoggerInterface;

class AuthController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/signup', name: 'app_signup')]
public function signup(
    Request $request,
    EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $passwordHasher,
    SmsService $smsService
): Response {
    $user = new Users();
    $user->setRole("candidat");

    $form = $this->createForm(SignUpType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $plainPassword = $user->getPassword();
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // Gérer l'upload de l'image
        $avatarFile = $form->get('avatar')->getData();
        if ($avatarFile) {
            $newFilename = uniqid() . '.' . $avatarFile->guessExtension();
            try {
                $avatarFile->move(
                    $this->getParameter('avatars_directory'),
                    $newFilename
                );
                $user->setAvatar($newFilename);
            } catch (FileException $e) {
                $this->logger->error('Erreur upload image: ' . $e->getMessage());
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                return $this->render('auth/signup.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        }

        // Enregistrer l'utilisateur
        try {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->logger->info('Utilisateur inscrit: ' . $user->getEmail());
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'enregistrement: ' . $e->getMessage());
            $this->addFlash('error', 'Erreur lors de l\'inscription. Veuillez réessayer.');
            return $this->render('auth/signup.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        // Tenter d'envoyer le SMS
        try {
            $phoneNumber = $this->formatPhoneNumber($user->getTel());
            $message = sprintf(
                "Bienvenue sur TuniRh!\nEmail: %s\nMot de passe temporaire: %s",
                $user->getEmail(),
                $plainPassword
            );
            $smsService->sendSms($phoneNumber, $message);
        } catch (\InvalidArgumentException $e) {
            $this->logger->error('Erreur format numéro: ' . $e->getMessage());
            $this->addFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->critical('Erreur SMS: ' . $e->getMessage());
            $this->addFlash('warning', 'Inscription réussie, mais échec de l\'envoi du SMS.');
        }

        $this->addFlash('success', 'Inscription réussie ! Connectez-vous maintenant.');
        return $this->redirectToRoute('app_login');
    }

    return $this->render('auth/signup.html.twig', [
        'form' => $form->createView(),
    ]);
}

    // Les autres méthodes restent inchangées
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class);

        return $this->render('auth/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, EntityManagerInterface $entityManager, NotificationService $notificationService, SessionInterface $session): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $this->logger->info('Demande de réinitialisation pour email: ' . $email);
            $user = $entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);

            if ($user) {
                // Générer un code de vérification aléatoire (6 chiffres)
                $code = sprintf("%06d", mt_rand(0, 999999));
                $this->logger->info('Code généré: ' . $code);

                // Stocker le code et l'email en session
                $session->set('reset_password_code', $code);
                $session->set('reset_password_email', $email);
                $session->set('reset_code_expires_at', time() + 1800); // Expire dans 30 minutes

                // Envoyer l'email avec le code
                try {
                    $notificationService->sendVerificationCodeEmail($user, $code);
                    $this->logger->info('Email envoyé à: ' . $email);
                    $this->addFlash('success', 'Un code de vérification a été envoyé à votre adresse email.');
                    return $this->redirectToRoute('app_verify_code', ['email' => $email]);
                } catch (\Throwable $e) {
                    $this->logger->error('Échec envoi email: ' . $e->getMessage());
                    $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer.');
                }
            } else {
                $this->logger->warning('Aucun utilisateur trouvé pour email: ' . $email);
                $this->addFlash('error', 'Aucun utilisateur trouvé avec cet email.');
            }
        }

        return $this->render('auth/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/verify-code', name: 'app_verify_code')]
    public function verifyCode(Request $request, SessionInterface $session): Response
    {
        $email = $request->query->get('email');
        $form = $this->createForm(VerifyCodeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $code = $form->get('code')->getData();
            $storedCode = $session->get('reset_password_code');
            $storedEmail = $session->get('reset_password_email');
            $expiresAt = $session->get('reset_code_expires_at');

            $this->logger->info('Vérification code: ' . $code . ', stocké: ' . $storedCode . ', email: ' . $email);

            if ($code === $storedCode && $email === $storedEmail && time() <= $expiresAt) {
                $this->logger->info('Code valide pour email: ' . $email);
                return $this->redirectToRoute('app_reset_password');
            } else {
                $this->logger->warning('Code invalide ou expiré pour email: ' . $email);
                $this->addFlash('error', 'Code invalide ou expiré.');
            }
        }

        return $this->render('auth/verify_code.html.twig', [
            'form' => $form->createView(),
            'email' => $email,
        ]);
    }

    #[Route('/reset-password', name: 'app_reset_password')]
    public function resetPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, SessionInterface $session): Response
    {
        $email = $session->get('reset_password_email');
        if (!$email) {
            $this->logger->warning('Session expirée pour réinitialisation mot de passe.');
            $this->addFlash('error', 'Session expirée. Veuillez recommencer.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $user = $entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);
        if (!$user) {
            $this->logger->warning('Utilisateur introuvable pour email: ' . $email);
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $entityManager->flush();

            // Supprimer les données de session
            $session->remove('reset_password_code');
            $session->remove('reset_password_email');
            $session->remove('reset_code_expires_at');

            $this->logger->info('Mot de passe réinitialisé pour email: ' . $email);
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('auth/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/test-email', name: 'app_test_email')]
    public function testEmail(NotificationService $notificationService): Response
    {
        $user = new Users();
        $user->setEmail('hamraouissamar@gmail.com');
        try {
            $notificationService->sendVerificationCodeEmail($user, '123456');
            $this->logger->info('Test email envoyé à hamraouissamar@gmail.com');
            return new Response('Email envoyé avec succès !');
        } catch (\Throwable $e) {
            $this->logger->error('Erreur test email: ' . $e->getMessage());
            return new Response('Erreur lors de l\'envoi: ' . $e->getMessage(), 500);
        }
    }

    private function formatPhoneNumber(string $number): string
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        
        try {
            $numberProto = $phoneUtil->parse($number, 'TN');
            
            if (!$phoneUtil->isValidNumberForRegion($numberProto, 'TN')) {
                throw new \InvalidArgumentException('Numéro tunisien invalide');
            }
            
            if ($phoneUtil->getNumberType($numberProto) !== PhoneNumberType::MOBILE) {
                throw new \InvalidArgumentException('Doit être un numéro mobile');
            }

            return $phoneUtil->format($numberProto, PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            $errorMap = [
                NumberParseException::INVALID_COUNTRY_CODE => 'Code pays invalide',
                NumberParseException::NOT_A_NUMBER => 'Pas un numéro valide',
                NumberParseException::TOO_SHORT_NSN => 'Numéro trop court',
                NumberParseException::TOO_LONG => 'Numéro trop long'
            ];
            
            $errorMessage = $errorMap[$e->getErrorType()] ?? 'Erreur inconnue';
            throw new \InvalidArgumentException($errorMessage, 0, $e);
        }
    }
}