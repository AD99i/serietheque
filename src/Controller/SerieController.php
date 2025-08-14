<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    #[Route('/serie/list', name: 'list')]
    public function list(SerieRepository $serieRepository): Response
    {
        {
            $series = $serieRepository->findAll();
            //dd($series);

            return $this->render('serie/list.html.twig', [
                'series' => $series,
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
}
