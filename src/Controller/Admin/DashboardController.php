<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Utils\TranslatorTrait;
use App\Entity\Avatar\Avatar;
use App\Entity\Editor\Editor;
use App\Entity\Event\Event;
use App\Entity\Genre\Genre;
use App\Entity\System\System;
use App\Entity\Table\Table;
use App\Entity\User\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    // $this->trans('');
    use TranslatorTrait;

    #[Route('/oversight')]
    public function index(): Response
    {
        // you can also render some template to display a proper Dashboard
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        return $this->render('@bundles/EasyAdmin/page/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTranslationDomain('admins')
            ->setTitle($this->trans('admin.chaotic_role_player'))
            ->setFaviconPath('/build/front/favicon/favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute($this->trans('admin.home'), 'fa fa-home', 'home.index');
        yield MenuItem::linkToDashboard($this->trans('admin.dashboard'), 'fas fa-columns');
        yield MenuItem::section($this->trans('admin.management'));
        yield MenuItem::linkToCrud($this->trans('admin.user', ['%count%' => 2]), 'fas fa-users', User::class);
        yield MenuItem::linkToCrud($this->trans('admin.avatar', ['%count%' => 2]), 'fas fa-image', Avatar::class);
        yield MenuItem::section($this->trans('admin.role_play'));
        yield MenuItem::linkToCrud($this->trans('admin.table', ['%count%' => 2]), 'fas fa-calendar', Table::class);
        yield MenuItem::linkToCrud($this->trans('admin.genre', ['%count%' => 2]), 'fas fa-film', Genre::class);
        yield MenuItem::linkToCrud($this->trans('admin.editor', ['%count%' => 2]), 'fas fa-newspaper', Editor::class);
        yield MenuItem::linkToCrud($this->trans('admin.system', ['%count%' => 2]), 'fas fa-dice', System::class);
        yield MenuItem::linkToCrud($this->trans('admin.event', ['%count%' => 2]), 'fas fa-calendar-days', Event::class);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getUsername())
            ->displayUserAvatar(false)
            ->addMenuItems([
                MenuItem::linkToRoute($this->trans('admin.profile'), 'fa fa-id-card', 'front.account.index'),
                MenuItem::linkToExitImpersonation('Stop impersonation', 'fa fa-door-open')
            ]);
    }
}
