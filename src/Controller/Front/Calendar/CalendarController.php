<?php

namespace App\Controller\Front\Calendar;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(['/calendar'])]
class CalendarController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route(name: 'calendar.index')]
    public function index(): Response
    {
        return $this->render('@front/calendar/index.html.twig');
    }
}
