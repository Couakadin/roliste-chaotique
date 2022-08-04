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

class ContactController extends AbstractController
{
    public function __construct(private readonly EmailAdmin $emailAdmin, private readonly TranslatorInterface $translator) { }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/contact', name: 'contact.index')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $request->request->all('contact_form')['email'];
            $subject = $request->request->all('contact_form')['subject'];
            $message = $request->request->all('contact_form')['message'];

            $this->emailAdmin->newContactAdmin($email, $subject, $message);

            $this->addFlash('success', $this->translator->trans('flash.contact.success'));

            return $this->redirectToRoute('security.contact.index');
        }

        return $this->render('@front/security/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}