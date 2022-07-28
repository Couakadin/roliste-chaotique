<?php

namespace App\Controller\Front\Account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="front.account.index")
     */
    public function index(): Response
    {
        return $this->render('@front/account/index.html.twig');
    }
}
