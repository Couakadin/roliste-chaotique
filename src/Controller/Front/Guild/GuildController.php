<?php

namespace App\Controller\Front\Guild;

use App\Entity\Guild\Guild;
use App\Form\Guild\GuildProfileType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class GuildController extends AbstractController
{
    public function __construct(public EntityManagerInterface $entityManager, public TranslatorInterface $translator)
    {
    }

    /**
     * @Route("/guilds", name="front.guild.index")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $repo = $this->entityManager->getRepository(Guild::class);

        $guilds = $paginator->paginate($repo->findBy([], ['createdAt' => 'DESC']), $request->query->getInt('page', 1), 15);

        return $this->render('@front/guild/index.html.twig', [
            'guilds' => $guilds
        ]);
    }

    /**
     * @Route("/guilds/guild/{slug}", name="front.guild.show")
     * @param string $slug
     * @return Response
     * @throws Exception
     */
    public function show(string $slug): Response
    {
        $guildRepo = $this->entityManager->getRepository(Guild::class);
        $guild = $guildRepo->findOneBy(['slug' => $slug]);

        if (!$guild) {
            return $this->redirectToRoute('front.guild.index');
        }

        return $this->render('@front/guild/show.html.twig', [
            'guild' => $guild
        ]);
    }

    /*
    /**
     * @Route("/guilds/edit/{slug}", name="front.guild.edit")
     * @param string $slug
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     * @throws Exception
     */
    /*
    public function edit(string $slug, Request $request, FileUploader $fileUploader): Response
    {
        $guildRepo = $this->entityManager->getRepository(Guild::class);
        $guild = $guildRepo->findOneBy(['slug' => $slug]);

        if (!$guild) {
            return $this->redirectToRoute('front.guild.index');
        }

        if ($this->getUser() !== $guild->getMaster()) {
            return $this->redirectToRoute('front.guild.index');
        }

        $form = $this->createForm(GuildProfileType::class, $guild);

        // Store the filename to reuse it when the picture is set
        $formPicture = $guild->getPicture();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();

            $file = $form->get('picture')->getData();
            if (!is_null($file)) {
                $newFile = $fileUploader->upload($this->getParameter('guild_directory'), $file, $formPicture);
                $guild->setPicture($newFile);
            } else {
                $guild->setPicture($formPicture);
            }

            $this->entityManager->flush();

            $this->addFlash('success', ucfirst($this->translator->trans('flash.guild.edit')));
            return $this->redirectToRoute('front.guild.edit', ['slug' => $guild->getSlug()]);
        }

        return $this->render('@front/guild/edit.html.twig', [
            'guild' => $guild,
            'form'  => $form->createView()
        ]);
    }
    */

    /**
     * @Route("/guilds/new", name="front.guild.new")
     * @throws Exception
     */
    /*
    public function new(Request $request, FileUploader $fileUploader, SluggerInterface $slugger): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login.index');
        }

        $guild = new Guild();
        $formPicture = $guild->getPicture();

        $form = $this->createForm(GuildProfileType::class, $guild);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->setMaster($this->getUser());
            $data->setSlug($slugger->slug(strtolower($data->getName())));

            $file = $form->get('picture')->getData();
            if (!is_null($file)) {
                $newFile = $fileUploader->upload($this->getParameter('guild_directory'), $file, $formPicture);
                $guild->setPicture($newFile);
            } else {
                $guild->setPicture($formPicture);
            }

            $this->entityManager->persist($data);
            $this->entityManager->flush();

            $this->addFlash('success', ucfirst($this->translator->trans('flash.guild.new', [
                '%guild%' => $guild->getName()
            ])));

            return $this->redirectToRoute('front.guild.edit', ['slug' => $guild->getSlug()]);
        }

        return $this->render('@front/guild/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    */
}
