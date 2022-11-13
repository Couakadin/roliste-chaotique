<?php

namespace App\Controller\Front\Account;

use App\Entity\Folder\Folder;
use App\Entity\Storage\Storage;
use App\Form\Folder\FolderType;
use App\Form\Storage\StorageType;
use App\Repository\Folder\FolderRepository;
use App\Repository\Storage\StorageRepository;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account')]
class StorageController extends AbstractController
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param StorageRepository $storageRepository
     * @param FolderRepository $folderRepository
     * @param UserRepository $userRepository
     */
    public function __construct
    (
        private readonly EntityManagerInterface $entityManager,
        private readonly StorageRepository      $storageRepository,
        private readonly FolderRepository       $folderRepository,
        private readonly UserRepository         $userRepository,
    )
    {
    }

    /**
     * @param Request $request
     * @param string|null $slug
     * @param string|null $folder
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/{slug}/storage', name: 'account.storage')]
    #[Route('/{slug}/storage/{folder}', name: 'account.storage')]
    public function storage(Request $request, string $slug = null, string $folder = null): Response
    {
        // Find User
        $user = $this->userRepository->find($this->getUser());

        if ($user?->getSlug() !== $slug) {
            return $this->redirectToRoute('account.storage', ['slug' => $user?->getSlug()]);
        }

        $folderFind = $this->folderRepository->findOneBy(['owner' => $user, 'slug' => $folder]);
        $folderPath = $folderFind ? $this->folderRepository->getPath($folderFind) : null;

        $storages = $this->storageRepository->findBy(['user' => $user, 'folder' => $folderFind]);

        // Upload new file
        $formStorage = $this->createForm(StorageType::class, $storageNew = new Storage());
        $formStorage->handleRequest($request);
        if ($formStorage->isSubmitted() && $formStorage->isValid()) {
            $storageNew->setFolder($folderFind);

            $this->entityManager->persist($storageNew);
            $this->entityManager->flush();

            return $this->redirectToRoute('account.storage', [
                'slug' => $user?->getSlug(),
                'folder' => $storageNew->getFolder()?->getSlug()
            ]);
        }

        // Create new folder
        $newFolder = new Folder();
        $formNewFolder = $this->createForm(FolderType::class, $newFolder, [
            'action' => $this->generateUrl('account.folder-new', [
                'parentSlug' => $folderFind?->getSlug()
            ])
        ]);

        return $this->renderForm('@front/account/storage.html.twig', [
            'formStorage'      => $formStorage,
            'formNewFolder'    => $formNewFolder,
            'folders'          => $folderFind?->getSlug(),
            'storages'         => $storages,
            'path'             => $folderPath,
            'folderHierarchy'  => $this->folderRepository->buildTree($this->folderRepository->getTreeQuery(
                $user,
                $folderFind
            )),
            'totalSizeStorage' => $this->storageRepository->getTotalSizePerUser($this->getUser()) ?? 0,
        ]);
    }

    /**
     * @param Request $request
     * @param string $storage
     *
     * @return Response
     */
    #[Route('/storage/edit-storage/{storage}', name: 'account.storage-edit')]
    public function editStorage(Request $request, string $storage): Response
    {
        // Find User
        $user = $this->userRepository->find($this->getUser());
        // Find storage to edit
        $storageEdited = $this->storageRepository->findOneBy(['slug' => $storage, 'user' => $user]);

        if (!$storageEdited) {
            return $this->redirectToRoute('account.storage', ['slug' => $user?->getSlug()]);
        }

        $formEditStorage = $this->createForm(StorageType::class, $storageEdited);
        $formEditStorage->handleRequest($request);

        if ($formEditStorage->isSubmitted() && $formEditStorage->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('account.storage', [
                'slug' => $user?->getSlug(),
                'folder' => $storageEdited->getFolder()->getSlug()
            ]);
        }

        return $this->renderForm('@front/account/storage/edit.html.twig', [
            'form' => $formEditStorage,
            'storage' => $storageEdited
        ]);
    }

    /**
     * @param Request $request
     * @param string $storage
     *
     * @return RedirectResponse
     */
    #[Route('/storage/delete-storage/{storage}', name: 'account.storage-delete', methods: ['DELETE'])]
    public function deleteStorage(Request $request, string $storage): RedirectResponse
    {
        // Find User
        $user = $this->userRepository->find($this->getUser());
        // Find storage to delete
        $storageDeleted = $this->storageRepository->findOneBy(['slug' => $storage, 'user' => $user]);

        if (!$storageDeleted) {
            return $this->redirectToRoute('account.storage', ['slug' => $user?->getSlug()]);
        }

        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-storage', $submittedToken)) {
            $this->entityManager->remove($storageDeleted);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('account.storage', [
            'slug' => $user?->getSlug()
        ]);
    }
}
