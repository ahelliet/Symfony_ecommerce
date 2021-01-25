<?php

namespace App\Controller;

use DateTime;
use App\Entity\Annonce;
use App\Entity\Enchere;
use App\Form\EnchereType;
use App\Repository\UserRepository;
use App\Repository\AnnonceRepository;
use App\Repository\EnchereRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/encheres")
 */
class EnchereController extends AbstractController
{
    /**
     * @Route("/", name="enchere_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(EnchereRepository $enchereRepository): Response
    {
        return $this->render('enchere/index.html.twig', [
            'encheres' => $enchereRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="enchere_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, AnnonceRepository $annonceRepository): Response
    {   
        $enchere = new Enchere();
        $response = new Response();
        $user = $this->getUser();
        $annonce = $annonceRepository->findOneBy(['id'=>$request->query->get('annonce_id')]);

        $enchere->setAuctionValue($request->query->get('enchere_value'));
        $enchere->setUser($user);
        $enchere->setAnnonce($annonce);
        $enchere->setCreatedAt(new \DateTime('now'));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($enchere);
        $entityManager->flush();

        $response->headers->set('content-type','application/json');
        $response->setContent(json_encode(['Statut'=>'Enchère ajoutée']));
        return $response;
    }

    /**
     * @Route("/{id}", name="enchere_show", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function show(Enchere $enchere): Response
    {
        return $this->render('enchere/show.html.twig', [
            'enchere' => $enchere,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="enchere_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Enchere $enchere): Response
    {
        $form = $this->createForm(EnchereType::class, $enchere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('enchere_index');
        }

        return $this->render('enchere/edit.html.twig', [
            'enchere' => $enchere,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="enchere_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Enchere $enchere): Response
    {
        if ($this->isCsrfTokenValid('delete'.$enchere->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($enchere);
            $entityManager->flush();
        }

        return $this->redirectToRoute('enchere_index');
    }
}
