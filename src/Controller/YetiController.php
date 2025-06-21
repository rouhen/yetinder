<?php

namespace App\Controller;

use App\Entity\Yeti;
use App\Form\YetiType;
use App\Repository\YetiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class YetiController extends AbstractController
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    #[Route('/', name: 'yeti_list')]
    public function dashboard(YetiRepository $yetiRepository): Response
    {
        $previousWeek = new \DateTime('-1 week');

        return $this->render('yeti/index.html.twig', [
            'totalYeti' => $yetiRepository->count([]),
            'newYetisLastWeek' => $yetiRepository->countNewSince($previousWeek),
            'topYetis' => $yetiRepository->findTopByVotes(5),
            'avgYetiVotes' => $yetiRepository->getAverageVotes()
        ]);
    }

    #[Route('/best', name: 'yeti_best_of')]
    public function bestOf(YetiRepository $yetiRepository): Response
    {
        $yetis = $yetiRepository->findBy([], ['votes' => 'DESC'], 10) ?: [];

        return $this->render('yeti/bestOf.html.twig', [
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
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('yeti_images_directory'),
                    $newFilename
                );
                $yeti->setImage($newFilename);
            }

            $em->persist($yeti);
            $em->flush();

            $this->addFlash('success', $this->translator->trans('Yeti has been successfully created!'));
            return $this->redirectToRoute('yeti_list');
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
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('yeti_images_directory'),
                    $newFilename
                );
                $yeti->setImage($newFilename);
            }

            $em->flush();

            $this->addFlash('success', $this->translator->trans('Yeti has been successfully updated!'));
            return $this->redirectToRoute('yeti_list');
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
