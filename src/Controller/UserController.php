<?php

namespace App\Controller;

use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_user_home")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    /**
     * @Route("/annonces", name="app_user_annonces")
     */
    public function mesAnnonces(AnnonceRepository $annonceRepository): Response
    {
        $user = $this->getUser();
        return $this->render('user/annonces.html.twig', [
            'controller_name' => 'UserController',
            'annonces' => $annonceRepository->findBy(['user' => $user])
        ]);
    }
    /**
     * @Route("/encheres", name="app_user_encheres")
     */
    public function mesEncheres(): Response
    {
        return $this->render('user/encheres.html.twig', [
            'controller_name' => 'UserController',
            'encheres' => 'mes encheres'
        ]);
    }
}
