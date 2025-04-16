<?php

namespace App\Controller;

use App\Entity\Candidatures;
use App\Entity\Offresemploi;
use App\Form\CandidaturesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

#[Route('/candidatures')]
final class CandidaturesController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/offres', name: 'app_offres_index', methods: ['GET'])]
    public function indexOffres(Request $request, EntityManagerInterface $em): Response
    {
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

        $offres = $queryBuilder->getQuery()->getResult();

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
            'offres' => $offres,
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
        EntityManagerInterface $em
    ): Response {
        $candidature = new Candidatures();
        // Définir offre et date_candidature avant la validation
        $candidature
            ->setOffre($offre)
            ->setDateCandidature(new \DateTime());

        $form = $this->createForm(CandidaturesType::class, $candidature);
        
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $cvFile = $form->get('cvFile')->getData();
                    if ($cvFile) {
                        $this->logger->debug('CV file received', ['extension' => $cvFile->guessExtension(), 'size' => $cvFile->getSize()]);
                        $fileName = md5(uniqid()).'.'.$cvFile->guessExtension();
                        $cvFile->move($this->getParameter('cv_directory'), $fileName);
                        $candidature->setCv($fileName);
                    } else {
                        $this->logger->debug('No CV file uploaded, proceeding without CV');
                        // Ne pas bloquer si aucun CV (cohérent avec required => false)
                    }

                    $em->persist($candidature);
                    $em->flush();

                    $this->addFlash('success', 'Votre candidature a été envoyée avec succès !');
                    return $this->redirectToRoute('app_offres_index', [], Response::HTTP_SEE_OTHER);
                } catch (\Exception $e) {
                    $this->logger->error('Error uploading CV: '.$e->getMessage());
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement du CV.');
                }
            } else {
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $errors[] = $error->getMessage();
                }
                $this->logger->debug('Form validation errors', ['errors' => $errors]);
                $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            }
        }

        return $this->render('candidatures/postuler.html.twig', [
            'form' => $form->createView(),
            'offre' => $offre
        ]);
    }

    #[Route('/offres/{id_offre}/candidatures', name: 'app_candidatures_list_by_offre', methods: ['GET'])]
    public function listByOffre(Offresemploi $offre, EntityManagerInterface $em): Response
    {
        $candidatures = $em->getRepository(Candidatures::class)
                         ->findBy(['offre' => $offre], ['date_candidature' => 'DESC']);

        return $this->render('candidatures/list_by_offre.html.twig', [
            'offre' => $offre,
            'candidatures' => $candidatures
        ]);
    }
}