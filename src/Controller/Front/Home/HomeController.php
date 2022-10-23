<?php

namespace App\Controller\Front\Home;

use App\Entity\Table\Table;
use App\Service\BadgeManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(private readonly BadgeManager $badgeManager){}

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

    #[Route(['/penguin'], name: 'penguin.index')]
    public function penguin(): Response
    {
        // Unlock badge
        if ($this->getUser())
        $this->badgeManager->checkAndUnlock($this->getUser(), 'penguin', 1);

        return $this->redirectToRoute('home.index');
    }
}
