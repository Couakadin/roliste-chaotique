<?php

namespace App\Controller\Front\Account;

use App\Entity\Folder\Folder;
use App\Entity\Storage\Storage;
use App\Entity\User\User;
use App\Form\Folder\FolderType;
use App\Form\Storage\StorageType;
use App\Repository\Folder\FolderRepository;
use App\Repository\Storage\StorageRepository;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use League\Uri\Contracts\UserInfoInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/account')]
class StorageController extends AbstractController
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param StorageRepository $storageRepository
     * @param FolderRepository $folderRepository
     * @param UserRepository $userRepository
     * @param CsrfTokenManagerInterface $csrfTokenManager
     */
    public function __construct
    (
        private readonly EntityManagerInterface    $entityManager,
        private readonly StorageRepository         $storageRepository,
        private readonly FolderRepository          $folderRepository,
        private readonly UserRepository            $userRepository,
        private readonly CsrfTokenManagerInterface $csrfTokenManager
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

        $storageRepo = $this->storageRepository->findBy(['user' => $user, 'folder' => $folderFind]);

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
            ]),
            'method' => 'POST'
        ]);

        return $this->render('@front/account/storage.html.twig', [
            'formStorage' => $formStorage->createView(),
            'formNewFolder' => $formNewFolder->createView(),
            'folders' => $folderFind?->getSlug(),
            'path' => $folderPath,
            'folderHierarchy' => $this->folderRepository->buildTree(
                $this->folderRepository->getTreeQuery($this->getUser()),
                $this->getTreeOptions($user ?? $this->getUser())
            ),
            'storages' => $storageRepo,
            'totalSizeStorage' => $this->storageRepository->getTotalSizePerUser($this->getUser()) ?? 0
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

            return $this->redirectToRoute('account.storage', ['slug' => $user?->getSlug()]);
        }

        return $this->render('@front/account/storage/edit.html.twig', [
            'form' => $formEditStorage->createView(),
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

    /**
     * @param UserInfoInterface|User $user
     *
     * @return array
     */
    private function getTreeOptions(UserInfoInterface|User $user): array
    {
        return [
            'decorate' => true,
            'rootOpen' => static function (array $tree): ?string {
                if ([] !== $tree && 0 === $tree[0]['lvl']) {
                    return '<ul>';
                }

                return '<ul class="ml1">';
            },
            'nodeDecorator' => function ($node) use ($user) {
                $route = $this->generateUrl('account.storage', [
                    'slug' => $user?->getSlug(), 'folder' => $node['slug']
                ]);

                $titleFolder = $node['title'];
                $editFolder = $this->generateUrl('account.folder-edit', ['folder' => $node['slug']]);
                $deleteFolder = $this->generateUrl('account.folder-delete', ['folder' => $node['slug']]);
                $CSRFFolder = $this->csrfTokenManager->getToken('delete-folder');

                return "
                        <div class='flex space-between'>
                            <a href=\"$route\">$titleFolder</a>
                            <div class='dropdown'>
                                <span>&#8230;</span>
                                <div class='dropdown-content'>
                                    <a href='$editFolder'>Éditer</a>
                                    <form action='$deleteFolder' method='post'>
                                        <input type='hidden' name='_method' value='DELETE'>                             
                                        <input type='hidden' name='token' value='$CSRFFolder'>
                                        <button type='submit'>Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                       ";
            }
        ];
    }
}
