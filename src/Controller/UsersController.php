<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users')]
final class UsersController extends AbstractController
{
<<<<<<< HEAD
    #[Route('/', name: 'app_users_index', methods: ['GET'])] // Ajout de '/' pour la route index
    public function index(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(Users::class)->findAll();

        return $this->render('users/index.html.twig', [
            'users' => $users,
=======
    #[Route('/', name: 'app_users_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $search = $request->query->get('search', '');
        $role = $request->query->get('role', '');

        // Base DQL query
        $dql = 'SELECT u FROM App\Entity\Users u';

        // Add conditions for search and role
        $conditions = [];
        $parameters = [];

        if (!empty($search)) {
            $conditions[] = '(u.nom LIKE :search OR u.prenom LIKE :search OR u.email LIKE :search)';
            $parameters['search'] = '%' . $search . '%';
        }

        if (!empty($role) && in_array($role, ['Candidat', 'Employé'])) {
            $conditions[] = 'u.role = :role';
            $parameters['role'] = $role;
        }

        // Append conditions to DQL
        if (!empty($conditions)) {
            $dql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        // Create and execute query
        $query = $entityManager->createQuery($dql);
        foreach ($parameters as $key => $value) {
            $query->setParameter($key, $value);
        }

        $users = $query->getResult();

        return $this->render('users/index.html.twig', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
>>>>>>> origin/gestionUserReclamation
        ]);
    }

    #[Route('/new', name: 'app_users_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès !');
            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('users/new.html.twig', [
            'user' => $user,
<<<<<<< HEAD
            'form' => $form->createView(), // Utilisation de createView() pour Twig
=======
            'form' => $form->createView(),
>>>>>>> origin/gestionUserReclamation
        ]);
    }

    #[Route('/{id_user}', name: 'app_users_show', methods: ['GET'])]
    public function show(Users $user): Response
    {
        return $this->render('users/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id_user}/edit', name: 'app_users_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur mis à jour avec succès !');
            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
<<<<<<< HEAD
            'form' => $form->createView(), // Utilisation de createView() pour Twig
=======
            'form' => $form->createView(),
>>>>>>> origin/gestionUserReclamation
        ]);
    }

    #[Route('/{id_user}', name: 'app_users_delete', methods: ['POST'])]
    public function delete(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getIdUser(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succès !');
        }

        return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
    }
}