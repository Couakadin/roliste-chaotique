<?php

namespace App\Controller\Front\Home;

use App\Entity\Notification\Notification;
use App\Entity\Table\Table;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * This controller returns the homepage.
     *
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws Exception
     */
    #[Route(['/', '/home', '/homepage'], name: 'home.index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $table = $entityManager->getRepository(Table::class);

        return $this->render('@front/home/index.html.twig', [
            'tables' => $table->findShowcase(),
        ]);
    }

    /**
     * @throws JsonException
     */
    #[Route('/notifications', name: 'home.notifications', methods: 'post')]
    public function notifications(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $notification = $entityManager->getRepository(Notification::class)
            ->find($data['notification']);

        if ($notification) {
            $notification->setIsRead(true);
            $entityManager->flush();
        }

        return new JsonResponse('OK');
    }

    #[Route('/notifications/all', name: 'home.notifications_all', methods: 'post')]
    public function notificationsAll(EntityManagerInterface $entityManager): JsonResponse
    {
        $notifications = $entityManager->getRepository(Notification::class)
            ->findBy(['user' => $this->getUser(), 'isRead' => false]);

        if ($notifications) {
            foreach ($notifications as $notification) {
                $notification->setIsRead(true);
                $entityManager->flush();
            }
        }

        return new JsonResponse('OK');
    }
}
