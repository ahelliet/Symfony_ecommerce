<?php

namespace App\Controller;

use DateTime;
use App\Entity\Annonce;
use App\Entity\Enchere;
use App\Form\EnchereType;
use App\Repository\EnchereRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/enchere")
 */
class EnchereController extends AbstractController
{
    /**
     * @Route("/", name="enchere_index", methods={"GET"})
     */
    public function index(EnchereRepository $enchereRepository): Response
    {
        return $this->render('enchere/index.html.twig', [
            'encheres' => $enchereRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="enchere_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        $annonce = $this->getDoctrine()->getRepository(Annonce::class)->find(60);

        $enchere = new Enchere();
        $enchere->setUser($user);
        $enchere->setAnnonce($annonce);
        $enchere->setCreatedAt(new \DateTime('now'));
        
        $form = $this->createForm(EnchereType::class, $enchere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($enchere);
            $entityManager->flush();

            return $this->redirectToRoute('enchere_index');
        }

        return $this->render('enchere/new.html.twig', [
            'enchere' => $enchere,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="enchere_show", methods={"GET"})
     */
    public function show(Enchere $enchere): Response
    {
        return $this->render('enchere/show.html.twig', [
            'enchere' => $enchere,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="enchere_edit", methods={"GET","POST"})
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
