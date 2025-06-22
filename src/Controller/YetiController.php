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

    #[Route('/', name: 'homepage')]
    public function homepage(Request $request): Response
    {
        return $this->redirectToRoute('yeti_dashboard', ['_locale' => $request->getLocale()]);
    }

    #[Route('/{_locale}/', name: 'yeti_dashboard', requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
    public function dashboard(YetiRepository $yetiRepository): Response
    {
        $previousWeek = new \DateTime('-1 week');

        return $this->render('yeti/index.html.twig', [
            'totalYeti' => $yetiRepository->count([]),
            'newYetisLastWeek' => $yetiRepository->countNewSince($previousWeek),
            'topYetis' => $yetiRepository->findTopByVotes(5),
            'avgYetiVotes' => $yetiRepository->getAverageVotes(),
        ]);
    }

    #[Route('/{_locale}/yeti/new', name: 'yeti_create', methods: ['GET', 'POST'], requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $yeti = new Yeti();
        $form = $this->createForm(YetiType::class, $yeti);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $imageFile */
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

            return $this->redirectToRoute('yeti_vote', ['_locale' => $request->getLocale()]);
        }

        return $this->render('yeti/edit.html.twig', [
            'form' => $form->createView(),
            'yeti' => $yeti,
        ]);
    }

    #[Route('/{_locale}/yeti/{id}/edit', name: 'yeti_edit', methods: ['GET', 'POST'], requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
    public function edit(Request $request, Yeti $yeti, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(YetiType::class, $yeti);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $imageFile */
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

            return $this->redirectToRoute('yeti_vote', ['_locale' => $request->getLocale()]);
        }

        return $this->render('yeti/edit.html.twig', [
            'form' => $form->createView(),
            'yeti' => $yeti,
        ]);
    }

    #[Route('/{_locale}/yeti/vote', name: 'yeti_vote', requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
    public function vote(YetiRepository $yetiRepository): Response
    {
        $yeti = $yetiRepository->findYetiForVote();

        return $this->render('yeti/vote.html.twig', [
            'yeti' => $yeti,
        ]);
    }

    #[Route('/{_locale}/yeti/{id}/vote/up', name: 'yeti_vote_up', methods: ['GET'], requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
    public function voteUp(Yeti $yeti, EntityManagerInterface $em, Request $request): Response
    {
        $yeti->setVotes($yeti->getVotes() + 1);
        $em->flush();

        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('yeti_vote', ['_locale' => $request->getLocale()]));
    }

    #[Route('/{_locale}/yeti/{id}/vote/dowm', name: 'yeti_vote_down', methods: ['GET'], requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
    public function voteDown(Yeti $yeti, EntityManagerInterface $em, Request $request): Response
    {
        $yeti->setVotes($yeti->getVotes() - 1);
        $em->flush();

        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('yeti_vote', ['_locale' => $request->getLocale()]));
    }

    #[Route('/{_locale}/yeti/best', name: 'yeti_best_of', requirements: ['_locale' => 'cs|en'], defaults: ['_locale' => 'cs'])]
    public function bestOf(YetiRepository $yetiRepository): Response
    {
        $yetis = $yetiRepository->findBy([], ['votes' => 'DESC'], 10) ?: [];

        return $this->render('yeti/bestOf.html.twig', [
            'yetis' => $yetis,
        ]);
    }
}
