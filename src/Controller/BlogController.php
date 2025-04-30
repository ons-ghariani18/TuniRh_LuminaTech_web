<?php

namespace App\Controller;

<<<<<<< HEAD
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }
=======
use App\Entity\Formation;
use App\Entity\Reservations;
use App\Entity\Users;
use App\Repository\FormationRepository;
use App\Repository\ReservationsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog', methods: ['GET'])]
    public function index(
        Request $request,
        FormationRepository $repo,
        ReservationsRepository $reservationsRepo
    ): Response {
        // 1) Récupération des filtres
        $search      = $request->query->get('search', null);
        $priceFilter = $request->query->get('price', 'all');

        // 2) Recherche + filtre back
        $formations = $repo->findBySearchAndPrice($search, $priceFilter);

        // 3) Réservations de l'utilisateur
        $user         = $this->getUser();
        $reservations = $user
            ? $reservationsRepo->findBy(['user' => $user])
            : [];

        // 4) Si AJAX → on renvoie du JSON structuré
        if ($request->isXmlHttpRequest()) {
            // Construire un set d'IDs réservés pour lookup rapide
            $reservedIds = array_map(fn($r) => $r->getFormation()->getId(), $reservations);

            $data = array_map(function($f) use ($reservedIds, $reservationsRepo) {
                return [
                    'id'             => $f->getId(),
                    'title'          => $f->getTitle(),
                    'description'    => $f->getDescription(),
                    'startDate'      => $f->getStartDate()?->format('Y-m-d'),
                    'endDate'        => $f->getEndDate()?->format('Y-m-d'),
                    'cost'           => $f->getCost(),
                    'imageUrl'       => '/img/blog-1.jpg',
                    'generateQuizPath'=>$this->generateUrl('generate_quiz_by_id', ['id' => $f->getId()]),
                    'isReserved'     => in_array($f->getId(), $reservedIds, true),
                    'detailPath'     => $this->generateUrl('app_formation_show', ['id' => $f->getId()]),
                    'status'         =>  $this->getUser() && in_array($f->getId(), $reservedIds, true)
                        ? $reservationsRepo->findOneBy(['user' =>  $this->getUser(), 'formation' => $f])->getStatus()
                        : null,
                ];
            }, $formations);

            return new JsonResponse($data);
        }

        // 5) Sinon rendu normal
        return $this->render('blog/index.html.twig', [
            'formations'   => $formations,
            'reservations' => $reservations,
            'priceFilter'  => $priceFilter,
        ]);
    }

    #[Route('/reservation/add', name: 'add_reservation', methods: ['POST'])]
    public function addReservation(
        Request $request,
        EntityManagerInterface $entityManager,
        FormationRepository $formationRepository
    ): Response {
        $user = $this->getUser();
        $formationId = $request->request->get('formation_id');
        $formation = $formationRepository->find($formationId);

        if (!$user || !$formation) {
            return new Response('Utilisateur ou formation non trouvé', 404);
        }

        // Vérifier si une réservation existe déjà
        $existingReservation = $entityManager->getRepository(Reservations::class)->findOneBy([
            'user' => $user,
            'formation' => $formation,
        ]);

        if ($existingReservation) {
            return new Response('Vous êtes déjà inscrit à cette formation.', 400);
        }

        $reservation = new Reservations();
        $reservation->setUser($user);
        $reservation->setFormation($formation);
        $reservation->setReservationDate(new \DateTime());
        $reservation->setStatus('en attente');

        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->redirectToRoute('app_blog');
    }

    #[Route('/reservation/remove', name: 'remove_reservation', methods: ['POST'])]
    public function removeReservation(
        Request $request,
        EntityManagerInterface $entityManager,
        FormationRepository $formationRepository
    ): Response {
        $user = $this->getUser();
        $formationId = $request->request->get('formation_id');
        $formation = $formationRepository->find($formationId);

        if (!$user || !$formation) {
            return new Response('Utilisateur ou formation non trouvé', 404);
        }

        $reservation = $entityManager->getRepository(Reservations::class)->findOneBy([
            'user' => $user,
            'formation' => $formation,
        ]);

        if (!$reservation) {
            return new Response('Aucune réservation trouvée pour cette formation.', 404);
        }

        $entityManager->remove($reservation);
        $entityManager->flush();

        return $this->redirectToRoute('app_blog');
    }

>>>>>>> origin/gestions-formations
}
