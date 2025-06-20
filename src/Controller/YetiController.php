<?php

namespace App\Controller;

use App\Entity\Yeti;
use App\Form\YetiType;
use App\Repository\YetiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class YetiController extends AbstractController
{
    #[Route('/', name: 'yeti_list')]
    public function index(YetiRepository $yetiRepository): Response
    {
        $yetis = $yetiRepository->findBy([], ['votes' => 'DESC'], 10) ?: [];

        return $this->render('yeti/index.html.twig', [
            'yetis' => $yetis,
        ]);
    }

    #[Route('/yeti/new', name: 'yeti_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $yeti = new Yeti();
        $form = $this->createForm(YetiType::class, $yeti);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($yeti);
            $em->flush();

            $this->addFlash('success', $this->trans('Yeti has been successfully created!'));
            return $this->redirectToRoute('yeti_edit', ['id' => $yeti->getId()]);
        }

        return $this->render('yeti/edit.html.twig', [
            'form' => $form->createView(),
            'yeti' => $yeti,
        ]);
    }

    #[Route('/yeti/{id}/edit', name: 'yeti_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Yeti $yeti, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(YetiType::class, $yeti);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', $this->trans('Yeti has been successfully updated!'));
            return $this->redirectToRoute('yeti_edit', ['id' => $yeti->getId()]);
        }

        return $this->render('yeti/edit.html.twig', [
            'form' => $form->createView(),
            'yeti' => $yeti,
        ]);
    }

    #[Route('/vote/up/{id}', name: 'yeti_vote_up', methods: ['GET'])]
    public function voteUp(Yeti $yeti, EntityManagerInterface $em): Response
    {
        $yeti->setVotes($yeti->getVotes() + 1);
        $em->flush();

        return $this->redirect($referer ?? $this->generateUrl('yeti_list'));
    }

    #[Route('/vote/down/{id}', name: 'yeti_vote_down', methods: ['GET'])]
    public function voteDown(Yeti $yeti, EntityManagerInterface $em): Response
    {
        $yeti->setVotes($yeti->getVotes() - 1);
        $em->flush();

        return $this->redirect($referer ?? $this->generateUrl('yeti_list'));
    }
}
