<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Helper\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/serie')]
#[IsGranted('ROLE_USER')]
final class SerieController extends AbstractController
{
    #[Route('/list-custom', name: 'custom_list')]
    public function listCustom(SerieRepository $serieRepository): Response
    {
        $series = $serieRepository->findSeriesCustom(400, 8);

        return $this->render('serie/list.html.twig', [
            'series' => $series,
            'page' => 1,
            'nbPerPage' => 10,
            'currentPage' => 1,
            'totalPages' => 1,
            'vote' => 8,
        ]);
    }

    #[Route('/list/{page}', name: 'serie_list', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function list(SerieRepository $serieRepository, int $page, ParameterBagInterface $parameterBag): Response
    {
        $nbPerPage = $parameterBag->get('serie')['nb_max'];
        $offset = ($page - 1) * $nbPerPage;

        $series = $serieRepository->getSeriesWithSeasons($nbPerPage, $offset);
        $total = $serieRepository->count([]);
        $totalPages = ceil($total / $nbPerPage);

        return $this->render('serie/list.html.twig', [
            'series' => $series,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'nbPerPage' => $nbPerPage,
        ]);
    }

    #[Route('/detail/{id}', name: 'serie_detail', requirements: ['id' => '\d+'])]
    public function detail(Serie $serie): Response
    {
        return $this->render('serie/detail.html.twig', [
            'serie' => $serie,
        ]);
    }

    #[Route('/create', name: 'serie_create')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, FileUploader $fileUploader, EntityManagerInterface $em, ParameterBagInterface $parameterBag): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('poster_file')->getData();
            if ($file instanceof UploadedFile) {
                $dir = $parameterBag->get('serie')['poster_directory'];
                $filename = $fileUploader->upload($file, $serie->getName(), $dir);
                $serie->setPoster($filename);
            }

            $serie->setDateCreated(new \DateTime());
            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'Série créée avec succès !');
            return $this->redirectToRoute('serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'serie_form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'serie_update')]
    #[IsGranted('ROLE_ADMIN')]
    public function update(Serie $serie, Request $request, FileUploader $fileUploader, EntityManagerInterface $em, ParameterBagInterface $parameterBag): Response
    {
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('poster_file')->getData();
            if ($file instanceof UploadedFile) {
                $dir = $parameterBag->get('serie')['poster_directory'];

                if ($serie->getPoster() && file_exists($dir . '/' . $serie->getPoster())) {
                    unlink($dir . '/' . $serie->getPoster());
                }

                $filename = $fileUploader->upload($file, $serie->getName(), $dir);
                $serie->setPoster($filename);
            }

            $em->flush();
            $this->addFlash('success', 'Série mise à jour avec succès !');
            return $this->redirectToRoute('serie_detail', ['id' => $serie->getId()]);
        }

        return $this->render('serie/edit.html.twig', [
            'serie_form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'serie_delete', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Serie $serie, EntityManagerInterface $em, Request $request, ParameterBagInterface $parameterBag): Response
    {
        if ($this->isCsrfTokenValid('delete' . $serie->getId(), $request->get('token'))) {
            $dir = $parameterBag->get('serie')['poster_directory'];
            if ($serie->getPoster() && file_exists($dir . '/' . $serie->getPoster())) {
                unlink($dir . '/' . $serie->getPoster());
            }

            $em->remove($serie);
            $em->flush();

            $this->addFlash('success', 'La série a été supprimée avec succès');
        } else {
            $this->addFlash('danger', 'Suppression impossible');
        }

        return $this->redirectToRoute('serie_list');
    }
}
