<?php

namespace App\Controller;

use App\Form\SearchAnnoncesType;
use App\Repository\AnnonceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request, AnnonceRepository $annonceRepository): Response
    {

        $annonces = $annonceRepository->findAll();

        $form = $this->createForm(SearchAnnoncesType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonces = $annonceRepository->SearchAnnonces($form->get('searchValue')->getData(), $form->get('category')->getData());
        }

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'annonces' => $annonces,
            'form' => $form->createView()
        ]);
    }
}
