<?php

namespace App\Controller\Front\Badge;

use App\Service\BadgeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BadgeController extends AbstractController
{
    public function __construct(private readonly BadgeManager $badgeManager){}

    #[Route(['/badge/unlock/dragon'], name: 'badge.dragon', methods: 'post')]
    public function dragon(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Unlock badge
        $this->badgeManager->checkAndUnlock($this->getUser(), 'dragon', 1);

        return $this->redirectToRoute('home.index');
    }

    #[Route(['/badge/unlock/konami'], name: 'badge.konami', methods: 'post')]
    public function konami(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Unlock badge
        $this->badgeManager->checkAndUnlock($this->getUser(), 'konami', 1);

        return $this->redirectToRoute('home.index');
    }
}