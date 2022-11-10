<?php

namespace App\Controller\Front\Account;

use App\Entity\Folder\Folder;
use App\Entity\Storage\Storage;
use App\Entity\User\User;
use App\Form\Folder\FolderType;
use App\Form\Storage\StorageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/account')]
class StorageController extends AbstractController
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @param CsrfTokenManagerInterface $csrfTokenManager
     */
    public function __construct
    (
        private readonly EntityManagerInterface  $entityManager,
        private readonly TranslatorInterface     $translator,
        private readonly CsrfTokenManagerInterface $csrfTokenManager
    )
    {
    }

    /**
     * @param Request $request
     * @param string|null $slug
     * @param string|null $folder
     * @return Response
     */
    #[Route('/{slug}/storage', name: 'account.storage')]
    #[Route('/{slug}/storage/{folder}', name: 'account.storage')]
    public function storage(Request $request, string $slug = null, string $folder = null): Response
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneBy(['slug' => $slug]);

        if ($user !== $this->getUser()) {
            return $this->redirectToRoute('account.storage', ['slug' => $this->getUser()->getSlug()]);
        }

        $folderRepo = $this->entityManager->getRepository(Folder::class);
        $folderFind = $folderRepo->findOneBy(['owner' => $user, 'slug' => $folder]);
        $folderPath = $folderFind ? $folderRepo->getPath($folderFind) : null;

        $storageRepo = $this->entityManager->getRepository(Storage::class)
            ->findBy(['user' => $user, 'folder' => $folderFind]);

        $array = [];
        foreach ($this->getUser()->getStorages() as $storage) {
            $array[] += $storage->getSize();
        }

        $formStorage = $this->createForm(StorageType::class, $storage = new Storage());
        $formStorage->handleRequest($request);

        if ($formStorage->isSubmitted() && $formStorage->isValid()) {
            $storage->setFolder($folderFind);

            $this->entityManager->persist($storage);
            $this->entityManager->flush();

            $this->addFlash('success', ucfirst($this->translator->trans('flash.account.upload')));

            return $this->redirectToRoute('account.storage', [
                'slug' => $user->getSlug(),
                'folder' => $storage->getFolder()?->getSlug()
            ]);
        }

        $newFolder = new Folder();
        $formNewFolder = $this->createForm(FolderType::class, $newFolder, [
            'action' => $this->generateUrl('account.folder-create', [
                'folder' => $folderFind?->getSlug()
            ]),
            'method' => 'POST'
        ]);

        $query = $this->entityManager
            ->createQueryBuilder()
            ->select('node')
            ->from(Folder::class, 'node')
            ->where('node.owner = :user')
            ->setParameter('user', $this->getUser())
            ->orderBy('node.root, node.lft', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $options = [
            'decorate'      => true,
            'rootOpen'      => static function (array $tree): ?string {
                if ([] !== $tree && 0 === $tree[0]['lvl']) {
                    return '<ul class="hierarchy">';
                }

                return '<ul class="hierarchy-root">';
            },
            'rootClose'     => '</ul>',
            'childOpen'     => static function (array $child): ?string {
                if ([] !== $child['__children']) {
                    return '<li class="hierarchy-item hierarchy-parent">';
                }

                return '<li class="hierarchy-item">';
            },
            'childClose'    => '</li>',
            'nodeDecorator' => function ($node) use ($request) {
                $route = $this->generateUrl('account.storage', [
                    'slug' => $this->getUser()->getSlug(), 'folder' => $node['slug']
                ]);

                $editFolder = $this->generateUrl('account.folder-edit', ['folder' => $node['slug']]);
                $deleteFolder = $this->generateUrl('account.folder-delete', ['folder' => $node['slug']]);

                $crsf = $this->csrfTokenManager->getToken('delete-folder');

                $title = $node['title'];

                return "
                        <a class='hierarchy-link dropdown-trigger' href=\"$route\">$title</a>
                        <div class='hierarchy-options dropdown'>
                            <span>&#9881;</span>
                            <div class='dropdown-content'>
                                <a class='folder-edit' href='$editFolder'>Éditer</a>
                                <form action='$deleteFolder' method='post'>
                                    <input type='hidden' name='_method' value='DELETE'>                             
                                    <input type='hidden' name='token' value='$crsf'>
                                    <button type='submit'>Supprimer</button>
                                </form>
                            </div>
                        </div>
                       ";
            }
        ];

        return $this->render('@front/account/storage.html.twig', [
            'formStorage'      => $formStorage->createView(),
            'formNewFolder'    => $formNewFolder->createView(),
            'folders'          => $folderFind?->getSlug(),
            'path'             => $folderPath,
            'folderHierarchy'  => $folderRepo->buildTree($query, $options),
            'storages'         => $storageRepo,
            'totalSizeStorage' => array_sum($array)
        ]);
    }

    #[Route('/storage/create-folder/{folder}', name: 'account.folder-create', methods: ['POST'])]
    public function createFolder(Request $request, string $folder = null): RedirectResponse
    {
        $folderRepo = $this->entityManager->getRepository(Folder::class)
            ->findOneBy(['slug' => $folder, 'owner' => $this->getUser()]);

        $newFolder = new Folder();
        $formNewFolder = $this->createForm(FolderType::class, $newFolder);
        $formNewFolder->handleRequest($request);

        if ($formNewFolder->isSubmitted() && $formNewFolder->isValid()) {
            $newFolder->setOwner($this->getUser());

            if ($folder) {
                $newFolder->setParent($folderRepo);
            }

            $this->entityManager->persist($newFolder);
            $this->entityManager->flush();

            $this->addFlash('success', ucfirst($this->translator->trans('flash.account.folder.create')));
        }

        return $this->redirectToRoute('account.storage', [
            'slug'   => $this->getUser()->getSlug(),
            'folder' => $folderRepo?->getSlug()
        ]);
    }

    #[Route('/storage/edit-folder/{folder}', name: 'account.folder-edit')]
    public function editFolder(Request $request, string $folder): Response
    {
        $folderRepo = $this->entityManager->getRepository(Folder::class)
            ->findOneBy(['slug' => $folder, 'owner' => $this->getUser()]);

        if (!$folderRepo) {
            return $this->redirectToRoute('account.storage', ['slug' => $this->getUser()->getSlug()]);
        }

        $formEditFolder = $this->createForm(FolderType::class, $folderRepo);
        $formEditFolder->handleRequest($request);

        if ($formEditFolder->isSubmitted() && $formEditFolder->isValid()) {
            $data = $formEditFolder->getData();
            $folderRepo->setSlug($data->getTitle());

            $this->entityManager->flush();

            $this->addFlash('success', ucfirst($this->translator->trans('flash.account.folder.edit')));

            return $this->redirectToRoute('account.storage', ['slug' => $this->getUser()->getSlug()]);
        }

        return $this->render('@front/account/storage/edit_folder.html.twig', [
            'form'   => $formEditFolder->createView(),
            'folder' => $folderRepo
        ]);
    }

    #[Route('/storage/delete-folder/{folder}', name: 'account.folder-delete', methods: ['DELETE'])]
    public function deleteFolder(Request $request, string $folder): RedirectResponse
    {
        $folderRepo = $this->entityManager->getRepository(Folder::class)
            ->findOneBy(['slug' => $folder, 'owner' => $this->getUser()]);

        if (!$folderRepo) {
            return $this->redirectToRoute('account.storage', ['slug' => $this->getUser()->getSlug()]);
        }

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-folder', $submittedToken)) {
            foreach ($folderRepo->getChildren() as $child) {
                $child->setParent(null);
            }

            foreach ($folderRepo->getStorages() as $storage) {
                $storage->setFolder(null);
            }

            $this->getUser()->removeFolder($folderRepo);
            $this->entityManager->remove($folderRepo);
            $this->entityManager->flush();

            $this->addFlash('success', ucfirst($this->translator->trans('flash.account.folder.delete')));
        }

        return $this->redirectToRoute('account.storage', [
            'slug'   => $this->getUser()->getSlug(),
            'folder' => $folderRepo->getParent() ?: null
        ]);
    }

    #[Route('/storage/edit-storage/{storage}', name: 'account.storage-edit')]
    public function editStorage(Request $request, string $storage): Response
    {
        $storageRepo = $this->entityManager->getRepository(Storage::class)
            ->findOneBy(['slug' => $storage, 'user' => $this->getUser()]);

        if (!$storageRepo) {
            return $this->redirectToRoute('account.storage', ['slug' => $this->getUser()->getSlug()]);
        }

        $formEditStorage = $this->createForm(StorageType::class, $storageRepo);
        $formEditStorage->handleRequest($request);

        if ($formEditStorage->isSubmitted() && $formEditStorage->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('account.storage', ['slug' => $this->getUser()->getSlug()]);
        }

        return $this->render('@front/account/storage/edit_storage.html.twig', [
            'form'   => $formEditStorage->createView(),
            'storage' => $storageRepo
        ]);
    }

    #[Route('/storage/delete-storage/{storage}', name: 'account.storage-delete', methods: ['DELETE'])]
    public function deleteStorage(Request $request, string $storage): RedirectResponse
    {
        $storageRepo = $this->entityManager->getRepository(Storage::class)
            ->findOneBy(['slug' => $storage, 'user' => $this->getUser()]);

        if (!$storageRepo) {
            return $this->redirectToRoute('account.storage', ['slug' => $this->getUser()->getSlug()]);
        }

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-storage', $submittedToken)) {
            $this->entityManager->remove($storageRepo);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('account.storage', [
            'slug'   => $this->getUser()->getSlug()
        ]);
    }
}
