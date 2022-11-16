<?php

namespace App\Controller\Front\Newsfeed;

use App\Entity\Newsfeed\Newsfeed;
use App\Form\Newsfeed\NewsfeedType;
use App\Repository\Newsfeed\NewsfeedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Turbo\TurboBundle;

#[Route('/newsfeed')]
class NewsfeedController extends AbstractController
{
    #[Route('/', name: 'newsfeed.index', methods: ['GET', 'POST'])]
    public function index(NewsfeedRepository $newsfeedRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        return $this->render('@front/newsfeed/index.html.twig', [
            'newsfeeds' => $newsfeedRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'newsfeed.new', methods: ['GET', 'POST'])]
    public function new(Request $request, NewsfeedRepository $newsfeedRepository): Response
    {
        $newsfeed = new Newsfeed();
        $form = $this->createForm(NewsfeedType::class, $newsfeed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newsfeedRepository->save($newsfeed, true);

            return $this->redirectToRoute('newsfeed.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('@front/newsfeed/new.html.twig', [
            'newsfeed' => $newsfeed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'newsfeed.show', methods: ['GET'])]
    public function show(Newsfeed $newsfeed): Response
    {
        return $this->render('@front/newsfeed/show.html.twig', [
            'newsfeed' => $newsfeed,
        ]);
    }

    #[Route('/{id}/edit', name: 'newsfeed.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Newsfeed $newsfeed, NewsfeedRepository $newsfeedRepository): Response
    {
        $form = $this->createForm(NewsfeedType::class, $newsfeed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newsfeedRepository->save($newsfeed, true);

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

                return $this->render('broadcast/Newsfeed/Newsfeed.stream.html.twig', ['id' => $newsfeed->getId()]);
            }

            return $this->redirectToRoute('newsfeed.show', [
                'id' => $newsfeed->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('@front/newsfeed/edit.html.twig', [
            'newsfeed' => $newsfeed,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'newsfeed.delete', methods: ['POST'])]
    public function delete(Request $request, Newsfeed $newsfeed, NewsfeedRepository $newsfeedRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$newsfeed->getId(), $request->request->get('_token'))) {
            $newsfeedRepository->remove($newsfeed, true);
        }

        return $this->redirectToRoute('newsfeed.index', [], Response::HTTP_SEE_OTHER);
    }
}
