<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SeasonController extends AbstractController
{
    #[Route('/season/create', name: 'season_create')]
    public function create(Request$request, EntityManagerInterface $em): Response
    {
        // Logique pour créer une saison
      $season =    new Season();

      $form = $this->createForm(SeasonType::class, $season);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($season);
            $em->flush();

            $this->addFlash('success', 'Saison créée avec succès !');
            return $this->redirectToRoute('serie_detail', ['id' => $season->getSerie()->getId()]);
        }

        return $this->render('season/edit.html.twig',[
            'season_form' => $form,
            'mode' => 'create',
        ]);
    }
}
