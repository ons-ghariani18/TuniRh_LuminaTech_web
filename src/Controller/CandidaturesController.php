<?php

namespace App\Controller;

use App\Entity\Candidatures;
use App\Entity\Offresemploi;
use App\Form\CandidaturesType;
use App\Service\MailerService;
use App\Service\SkillsMatchingService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/candidatures')]
final class CandidaturesController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/offres', name: 'app_offres_index', methods: ['GET'])]
    public function indexOffres(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ): Response {
        $queryBuilder = $em->getRepository(Offresemploi::class)->createQueryBuilder('o');

        if ($search = $request->query->get('search')) {
            $queryBuilder
                ->where('o.titre LIKE :search OR o.description LIKE :search')
                ->setParameter('search', "%$search%");
        }

        if ($typeContrat = $request->query->get('type_contrat')) {
            $queryBuilder
                ->andWhere('o.type_contrat = :type_contrat')
                ->setParameter('type_contrat', $typeContrat);
        }

        if ($lieu = $request->query->get('lieu')) {
            $queryBuilder
                ->andWhere('o.lieu = :lieu')
                ->setParameter('lieu', $lieu);
        }

        $queryBuilder->orderBy('o.date_publication', 'DESC');

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            6
        );

        $typesContrat = $em->getRepository(Offresemploi::class)
            ->createQueryBuilder('o')
            ->select('DISTINCT o.type_contrat')
            ->getQuery()
            ->getResult();
        $lieux = $em->getRepository(Offresemploi::class)
            ->createQueryBuilder('o')
            ->select('DISTINCT o.lieu')
            ->getQuery()
            ->getResult();

        return $this->render('candidatures/index.html.twig', [
            'pagination' => $pagination,
            'types_contrat' => array_column($typesContrat, 'type_contrat'),
            'lieux' => array_column($lieux, 'lieu'),
            'search' => $search,
            'selected_type_contrat' => $typeContrat,
            'selected_lieu' => $lieu,
        ]);
    }

    #[Route('/offres/{id_offre}', name: 'app_offres_show', methods: ['GET'])]
    public function showOffre(Offresemploi $offre): Response
    {
        return $this->render('candidatures/show.html.twig', [
            'offre' => $offre
        ]);
    }

    #[Route('/offres/{id_offre}/postuler', name: 'app_candidatures_postuler', methods: ['GET', 'POST'])]
    public function postuler(
        Request $request,
        Offresemploi $offre,
        EntityManagerInterface $em,
        MailerService $mailerService,
        SkillsMatchingService $skillsMatchingService
    ): Response {
        $candidature = new Candidatures();
        $candidature
            ->setOffre($offre)
            ->setDateCandidature(new \DateTime())
            ->setStatut('En attente');

        $form = $this->createForm(CandidaturesType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $cvFile = $form->get('cvFile')->getData();
                $cvPath = null;
                if ($cvFile) {
                    $this->logger->debug('CV file received', ['extension' => $cvFile->guessExtension(), 'size' => $cvFile->getSize()]);
                    $fileName = md5(uniqid()) . '.' . $cvFile->guessExtension();
                    $cvFile->move($this->getParameter('cv_directory'), $fileName);
                    $candidature->setCv($fileName);
                    $cvPath = $this->getParameter('cv_directory') . '/' . $fileName;
                }

                $jobSkillsText = $offre->getCompetencesRequises() ?? ($offre->getDescription() ?? '');
                $score = $skillsMatchingService->calculateCandidateScore('', $cvPath, $jobSkillsText);
                $candidature->setScore($score);

                $em->persist($candidature);
                $em->flush();

                $mailerService->sendConfirmationEmail(
                    $candidature->getEmail(),
                    $candidature->getNomCandidat(),
                    $offre->getTitre(),
                    $candidature->getDateCandidature()->format('d/m/Y'),
                    $cvPath
                );

                $this->addFlash('success', 'Votre candidature a été envoyée avec succès ! Score de correspondance : ' . ($candidature->getScore() ?? 'N/A') . '%');
                return $this->redirectToRoute('app_offres_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->logger->error('Error during candidature submission: ' . $e->getMessage());
                $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
            }
        } else if ($form->isSubmitted()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
            $this->logger->debug('Form validation errors', ['errors' => $errors]);
            $this->addFlash('error', 'Veuillez corriger les erreurs : ' . implode(', ', $errors));
        }

        return $this->render('candidatures/postuler.html.twig', [
            'form' => $form->createView(),
            'offre' => $offre,
        ]);
    }

    #[Route('/offres/{id_offre}/candidatures', name: 'app_candidatures_list_by_offre', methods: ['GET'])]
    public function listByOffre(Offresemploi $offre, EntityManagerInterface $em): Response
    {
        $candidatures = $em->getRepository(Candidatures::class)
            ->findBy(['offre' => $offre], ['dateCandidature' => 'DESC']);

        return $this->render('candidatures/list_by_offre.html.twig', [
            'offre' => $offre,
            'candidatures' => $candidatures
        ]);
    }
}