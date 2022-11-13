<?php

namespace App\Controller\Front\Security;

use App\Email\EmailAdmin;
use App\Form\Security\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/contact')]
class ContactController extends AbstractController
{
    /**
     * @param EmailAdmin $emailAdmin
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private readonly EmailAdmin          $emailAdmin,
        private readonly TranslatorInterface $translator
    )
    {
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws TransportExceptionInterface
     */
    #[Route(name: 'contact.index')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()['email'];
            $subject = $form->getData()['subject'];
            $message = $form->getData()['message'];

            // Inform admin for the new contact
            $this->emailAdmin->contactAdmin($email, $subject, $message);
            // Flash user contact confirmation
            $this->addFlash('success', $this->translator->trans('flash.contact.success'));

            return $this->redirectToRoute('contact.index');
        }

        return $this->renderForm('@front/security/contact.html.twig', [
            'form' => $form
        ]);
    }
}