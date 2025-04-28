<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\AppAuthenticator;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ?UserAuthenticatorInterface $authenticator,
        ?AppAuthenticator $appAuthenticator
    ): Response {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Set roles based on accountType
            $accountType = $form->get('accountType')->getData();
            if ($accountType === 'ROLE_ADMIN') {
                $user->setRoles(['ROLE_ADMIN']);
            } else {
                $user->setRoles(['ROLE_USER']);
            }

            // Persist the user
            $entityManager->persist($user);
            $entityManager->flush();

            // Authenticate the user if authenticator is available
            if ($authenticator && $appAuthenticator) {
                $response = $authenticator->authenticateUser(
                    $user,
                    $appAuthenticator,
                    $request
                );
                
                // After authentication, redirect based on account type
                if ($accountType === 'ROLE_ADMIN') {
                    return $this->redirectToRoute('app_back_office');
                }
                return $this->redirectToRoute('app_home');
            }

            // If no authenticator, redirect based on account type
            if ($accountType === 'ROLE_ADMIN') {
                return $this->redirectToRoute('app_back_office');
            }
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode sera interceptée par la clé de déconnexion dans le firewall.');
    }
}