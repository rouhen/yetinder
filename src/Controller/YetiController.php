<?php

namespace App\Controller;

use App\Entity\Yeti;
use App\Repository\YetiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class YetiController extends AbstractController
{
    #[Route('/', name: 'app_yeti')]
    public function index(YetiRepository $yetiRepository): Response
    {
        $yetis = $yetiRepository->findBy([], ['votes' => 'DESC'], 10) ?: [];

        return $this->render('yeti/index.html.twig', [
            'yetis' => $yetis,
        ]);
    }

    #[Route('/vote/up/{id}', name: 'vote_up', methods: ['GET'])]
    public function voteUp(Yeti $yeti, EntityManagerInterface $em): Response
    {
        $yeti->setVotes($yeti->getVotes() + 1);
        $em->flush();

        return $this->redirect($referer ?? $this->generateUrl('app_yeti'));
    }

    #[Route('/vote/down/{id}', name: 'vote_down', methods: ['GET'])]
    public function voteDown(Yeti $yeti, EntityManagerInterface $em): Response
    {
        $yeti->setVotes($yeti->getVotes() - 1);
        $em->flush();

        return $this->redirect($referer ?? $this->generateUrl('app_yeti'));
    }
}
