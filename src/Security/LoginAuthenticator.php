<?php
<<<<<<< HEAD
<<<<<<< HEAD

namespace App\Security;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
=======
=======
>>>>>>> origin/gestionOffre
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
<<<<<<< HEAD
<<<<<<< HEAD
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginAuthenticator extends AbstractAuthenticator
=======
=======
>>>>>>> origin/gestionOffre
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class LoginAuthenticator extends AbstractLoginFormAuthenticator
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

<<<<<<< HEAD
<<<<<<< HEAD
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $urlGenerator,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
=======
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
>>>>>>> origin/gestionOffre
=======
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
>>>>>>> origin/gestionOffre
    }

    public function authenticate(Request $request): Passport
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');

        return new Passport(
            new UserBadge($email, function ($userIdentifier) {
                return $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $userIdentifier]);
            }),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
=======
=======
>>>>>>> origin/gestionOffre
        // Récupérer toutes les données POST
        $postData = $request->request->all();

        // Essayer de récupérer les champs imbriqués sous 'login' ou directement
        $email = $postData['login']['email'] ?? $postData['email'] ?? '';
        $password = $postData['login']['password'] ?? $postData['password'] ?? '';
        $csrfToken = $postData['login']['_csrf_token'] ?? $postData['_csrf_token'] ?? '';

        if (empty($email)) {
            throw new CustomUserMessageAuthenticationException('L\'adresse email ne peut pas être vide.');
        }

        // Stocker l'email pour l'afficher en cas d'erreur
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
<<<<<<< HEAD
    {
<<<<<<< HEAD
<<<<<<< HEAD
=======
        // Rediriger vers la dernière URL tentée, si disponible
>>>>>>> origin/gestionOffre
=======
        // Rediriger vers la dernière URL tentée, si disponible
>>>>>>> origin/gestionOffre
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

<<<<<<< HEAD
<<<<<<< HEAD
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set('security.last_exception', $exception);

        return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
=======
=======
>>>>>>> origin/gestionOffre
        // Par défaut, rediriger vers la liste des réclamations
        return new RedirectResponse($this->urlGenerator->generate('app_reponse_index'));
    }

=======
{
    // Rediriger vers la dernière URL tentée, si disponible
    if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
        return new RedirectResponse($targetPath);
    }

    // ✅ Récupérer l'utilisateur connecté
    $user = $token->getUser();

    // ✅ Vérifier et rediriger selon le rôle
    if (in_array('Responsable RH', $user->getRoles())) {
        return new RedirectResponse($this->urlGenerator->generate('app_back_office')); // remplace par ta vraie route
    } elseif (in_array('ROLE_CANDIDAT', $user->getRoles()) || in_array('ROLE_EMPLOYE', $user->getRoles())) {
        return new RedirectResponse($this->urlGenerator->generate('app_home')); // ou ta page d'accueil
    }

    // ✅ Redirection fallback si aucun rôle ne correspond
    return new RedirectResponse($this->urlGenerator->generate('app_reponse_index'));
}


>>>>>>> origin/gestionUserReclamation
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
    }
}