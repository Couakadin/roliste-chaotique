<?php

namespace App\Controller\Front\Badge;

use App\Service\BadgeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(['/badge'])]
class BadgeController extends AbstractController
{
    /**
     * @param BadgeManager $badgeManager
     */
    public function __construct(private readonly BadgeManager $badgeManager) { }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[Route(['/unlock'], name: 'badge.unlock', methods: 'post')]
    public function unlock(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        (string)$badge = $request->query->get('badge');
        (string)$submittedToken = $request->request->get('token');

        // Unlock badge
        if (
            $badge &&
            $this->isCsrfTokenValid('badge-dragon', $submittedToken) ||
            $this->isCsrfTokenValid('badge-konami', $submittedToken) ||
            $this->isCsrfTokenValid('badge-riddle', $submittedToken)
        ) {
            $this->badgeManager->checkAndUnlock($this->getUser(), $badge, 1);

            return $this->redirectToRoute('account.badge', ['slug' => $this->getUser()->getSlug()]);
        }

        return $this->redirectToRoute('home.index');
    }
}
