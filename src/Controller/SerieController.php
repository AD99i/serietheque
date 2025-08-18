<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/serie')] // Route de base
final class SerieController extends AbstractController
{
    /*  #[Route('/serie', name: 'app_serie')]
      public function index(EntityManagerInterface $em): Response
      {

          $serie = new Serie();
          $serie->setName('One Piece')
              ->setStatus('En cours')
              ->setGenre('Anime')
              ->setFirstAirDate(new \DateTime('1999-10-20'))
              ->setDateCreated(new \DateTime());

          $em->persist($serie);
          $em->flush();




          return new Response('Une série a été créée !');
      }
      */

    #[Route('/list-custom',name:'custom_list',)]
    public function listCustom(SerieRepository $serieRepository):Response{

        $series = $serieRepository->findSeriesCustom(400,8);

        return $this->render('serie/list.html.twig', [
            'series' => $series,
            'page' => 1,
            'nbPerPage' => 10,
            'currentPage' => 1,
            'totalPages' => 1,
            'vote' => 8,
        ]);

    }
    #[Route('/list/{page}', name: '_list',
        requirements: ['page' => '\d+'],
        defaults: ['page' => 1],
        methods: ['GET'],
    )]
    public function list(SerieRepository $serieRepository,int $page,ParameterBagInterface $parameterBag): Response
    {

        $nbPerPage = $parameterBag->get('serie')['nb_max'];
        $offset = ($page - 1) * $nbPerPage;
        $criterias = []; // tableau qui permet de filtrer les séries si besoin à mettre dans findBy()
        // on le met en parametre du count() pour avoir le nombre total de séries

        {
            $series = $serieRepository->findBy(
                [],             // Aucun critère : on prend toutes les séries
                [],             // Aucun ordre spécifique
                $nbPerPage,     // Limite : nombre de séries par page
                $offset         // Décalage : pour paginer
            );

            $total = $serieRepository->count($criterias);
            $totalPages = ceil($total/$nbPerPage);
            $currentPage = $page;

            return $this->render('serie/list.html.twig', [
                'series' => $series,
                'currentPage' => $currentPage,
                'totalPages' => $totalPages,
                'nbPerPage' => $nbPerPage,
            ]);
        }
    }

    #[Route('/detail/{id}', name: '_detail')]
    public function detail(SerieRepository $serieRepository, int $id): Response
    {
        $serie = $serieRepository->find($id);

        if (!$serie) {
            throw $this->createNotFoundException('Série non trouvée');
        }

        return $this->render('serie/detail.html.twig', [
            'serie' => $serie,
        ]);
    }


        #[Route('/create', name: '_create')]
        public function create(Request $request): Response
    {

        $serie = new Serie(); // Création d'une nouvelle instance de la série
        $form = $this->createForm(SerieType::class,$serie); // Création du formulaire pour la création d'une série
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serie = $form->getData(); // Récupération des données du formulaire
            //$serie->setDateCreated(new \DateTime()); // Définition de la date de création
            $em = $this->getDoctrine()->getManager(); // Récupération du gestionnaire d'entité
            $em->persist($serie); // Préparation de l'entité pour l'insertion
            $em->flush(); // Envoi des données en base de données

            $this->addFlash('success', 'Série créée avec succès !'); // Message flash de succès

            return $this->redirectToRoute('serie_detail', ['id' => $serie->getId()]); // Redirection vers la liste des séries
        }

        return $this->render('serie/edit.html.twig',[
            'serie_form' => $form]);
    }


}
