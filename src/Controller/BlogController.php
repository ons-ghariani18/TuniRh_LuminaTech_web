<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Reservations;
use App\Entity\Users;
use App\Repository\FormationRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(FormationRepository $formationRepository): Response
    {

        return $this->render('blog/index.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }
    #[Route('/add', name: 'add_reservation')]
    public function addReservation(Request $request,UsersRepository $usersRepository,FormationRepository $formationRepository,EntityManagerInterface $entityManager): Response
    {    $userId = $request->query->get('user_id');
        $formationId = $request->query->get('formation_id');


        // Fetch user and formation from the DB
        $user =$usersRepository->find($userId);
        $formation = $formationRepository->find($formationId);

        if (!$user || !$formation) {
            return new Response('User or Formation not found', 404);
        }

        // Create and populate reservation
        $reservation = new Reservations();
        $reservation->setUser($user);
        $reservation->setFormation($formation);
        $reservation->setReservationDate(new \DateTime()); // default in constructor too
        $reservation->setStatus('pending'); // default in constructor too

        // Persist to DB
        $entityManager->persist($reservation);
        $entityManager->flush();

        return new Response('Reservation added successfully!');
    }

}
