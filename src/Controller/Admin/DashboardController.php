<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Utils\TranslatorTrait;
use App\Entity\Avatar\Avatar;
use App\Entity\Game\Game;
use App\Entity\Guild\Guild;
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

    /**
     * @Route("/oversight")
     */
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
        yield MenuItem::linkToRoute($this->trans('admin.home'), 'fa fa-home', 'front.home.index');
        yield MenuItem::linkToDashboard($this->trans('admin.dashboard'), 'fas fa-columns');
        yield MenuItem::section($this->trans('admin.management'));
        yield MenuItem::linkToCrud($this->trans('admin.user', ['%count%' => 2]), 'fas fa-users', User::class);
        yield MenuItem::linkToCrud($this->trans('admin.avatar', ['%count%' => 2]), 'fas fa-image', Avatar::class);
        yield MenuItem::section($this->trans('admin.role_play'));
        yield MenuItem::linkToCrud($this->trans('admin.game', ['%count%' => 2]), 'fas fa-layer-group', Game::class);
        yield MenuItem::linkToCrud($this->trans('admin.guild', ['%count%' => 2]), 'fas fa-gopuram', Guild::class);
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