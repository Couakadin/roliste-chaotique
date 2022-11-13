<?php

namespace App\Controller\Front\Account;

use App\Entity\Folder\Folder;
use App\Form\Folder\FolderType;
use App\Repository\Folder\FolderRepository;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account')]
class FolderController extends AbstractController
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param FolderRepository $folderRepository
     * @param UserRepository $userRepository
     */
    public function __construct
    (
        private readonly EntityManagerInterface $entityManager,
        private readonly FolderRepository       $folderRepository,
        private readonly UserRepository         $userRepository,
    )
    {
    }

    /**
     * @param Request $request
     * @param string|null $parentSlug
     *
     * @return Response
     */
    #[Route('/folder/new/{parentSlug}', name: 'account.folder-new')]
    public function new(Request $request, ?string $parentSlug = null): Response
    {
        // Find User
        $user = $this->userRepository->find($this->getUser());
        // Find if parent
        $parentFolder = $this->folderRepository->findOneBy(['slug' => $parentSlug, 'owner' => $this->getUser()]);

        $newFolder = new Folder();
        $formNewFolder = $this->createForm(FolderType::class, $newFolder);
        $formNewFolder->handleRequest($request);
        if ($formNewFolder->isSubmitted() && $formNewFolder->isValid()) {
            $newFolder->setParent($parentFolder);

            $this->entityManager->persist($newFolder);
            $this->entityManager->flush();
        }

        $controllerForward = 'App\Controller\Front\Account\StorageController::storage';

        return $this->forward($controllerForward, [
            'slug' => $user?->getSlug(),
            'folder' => $newFolder->getSlug()
        ]);
    }

    /**
     * @param Request $request
     * @param string $folder
     *
     * @return Response
     */
    #[Route('/folder/edit/{folder}', name: 'account.folder-edit')]
    public function edit(Request $request, string $folder): Response
    {
        // Find User
        $user = $this->userRepository->find($this->getUser());
        // Find folder to edit
        $folderEdited = $this->folderRepository->findOneBy(['slug' => $folder, 'owner' => $user]);

        if (!$folderEdited) {
            return $this->redirectToRoute('account.storage', ['slug' => $user?->getSlug()]);
        }

        $formEditFolder = $this->createForm(FolderType::class, $folderEdited);
        $formEditFolder->handleRequest($request);
        if ($formEditFolder->isSubmitted() && $formEditFolder->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('account.storage', [
                'slug' => $user?->getSlug(),
                'folder' => $folderEdited->getSlug()
            ]);
        }

        return $this->renderForm('@front/account/folder/edit.html.twig', [
            'form' => $formEditFolder,
            'folder' => $folderEdited
        ]);
    }

    /**
     * @param Request $request
     * @param string $folder
     *
     * @return RedirectResponse
     */
    #[Route('/folder/delete/{folder}', name: 'account.folder-delete', methods: ['DELETE'])]
    public function delete(Request $request, string $folder): RedirectResponse
    {
        // Find User
        $user = $this->userRepository->find($this->getUser());
        // Find folder to delete
        $folderDeleted = $this->folderRepository->findOneBy(['slug' => $folder, 'owner' => $user]);

        if (!$folderDeleted) {
            return $this->redirectToRoute('account.storage', ['slug' => $user?->getSlug()]);
        }

        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('delete-folder', $submittedToken)) {
            foreach ($folderDeleted->getChildren() as $child) {
                $child->setParent(null);
            }

            $this->entityManager->remove($folderDeleted);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('account.storage', [
            'slug' => $user?->getSlug(),
            'folder' => $folderDeleted->getParent()?->getSlug()
        ]);
    }
}
