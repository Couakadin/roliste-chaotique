<?php

namespace App\Controller\Front\Badge;

use App\Service\BadgeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BadgeController extends AbstractController
{
    public function __construct(private readonly BadgeManager $badgeManager){}

    #[Route(['/badge/unlock'], name: 'badge.unlock')]
    public function unlock(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $referer = $request->headers->get('referer');
        if (!$referer) {
            return $this->redirectToRoute('home.index');
        }

        (string) $badge = $request->query->get('badge');

        // Unlock badge
        $this->badgeManager->checkAndUnlock($this->getUser(), $badge, 1);

        return $this->redirect($referer);
    }
}
