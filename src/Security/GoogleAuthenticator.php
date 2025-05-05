<?php

namespace App\Security;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class GoogleAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    private $clientRegistry;
    private $entityManager;
    private $router;
    private $passwordHasher;

    public function __construct(
        ClientRegistry $clientRegistry, 
        EntityManagerInterface $entityManager, 
        RouterInterface $router,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->passwordHasher = $passwordHasher;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                $googleUser = $client->fetchUserFromToken($accessToken);
                $email = $googleUser->getEmail();
                $googleId = $googleUser->getId();

                $user = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);

                if (!$user) {
                    $user = new Users();
                    $user->setEmail($email);
                    $user->setNom($googleUser->getLastName() ?? 'Nom');
                    $user->setPrenom($googleUser->getFirstName() ?? 'Prénom');
                    $user->setGoogleId($googleId);
                    $user->setRole('candidat'); // Rôle par défaut pour un utilisateur Google
                    $user->setTel('00000000'); // Valeur par défaut (à ajuster selon vos besoins)
                    $user->setAddress('Adresse par défaut'); // Valeur par défaut (à ajuster selon vos besoins)
                    
                    // Generate a random secure password for OAuth users
                    $randomPassword = bin2hex(random_bytes(16)); // 32 characters random string
                    $hashedPassword = $this->passwordHasher->hashPassword($user, $randomPassword);
                    $user->setPassword($hashedPassword);
                    
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                } elseif (!$user->getGoogleId()) {
                    $user->setGoogleId($googleId);
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate('app_home')); // Redirige vers votre page de tableau de bord
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $this->saveAuthenticationErrorToSession($request, $exception);
        return new RedirectResponse($this->router->generate('app_login')); // Redirige vers la page de login en cas d'erreur
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse($this->router->generate('app_login')); // Point d'entrée si non authentifié
    }

    protected function saveAuthenticationErrorToSession(Request $request, AuthenticationException $exception): void
    {
        $request->getSession()->set('security.last_exception', $exception);
    }
} 