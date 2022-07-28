<?php

namespace App\Controller\Admin\User;

use App\Controller\Admin\Utils\TranslatorTrait;
use App\Entity\User\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    use TranslatorTrait;

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['name'])
            ->setPageTitle('new', fn() => $this->trans('admin.new_user'))
            ->setPageTitle('edit', fn(User $user) => sprintf('Modifier <b>%s</b>', $user->getUsername()))
            ->setPageTitle('index', $this->trans('admin.user', ['%count%' => 2]))
            ->setEntityLabelInPlural($this->trans('admin.user', ['%count%' => 2]))
            ->setEntityLabelInSingular($this->trans('admin.user', ['%count%' => 1]));
    }

    public function configureActions(Actions $actions): Actions
    {
        $impersonate = Action::new('impersonate', 'impersonate')
            ->displayAsLink()
            ->setCssClass('btn btn-warning')
            ->linkToUrl(function (User $user) {
                return './?_switch_user=' . $user->getEmail();
            });

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $impersonate)
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', $this->trans('admin.ui.id'))
                ->hideOnForm(),

            FormField::addPanel(''),
            AssociationField::new('avatar', $this->trans('admin.ui.avatar'))
                ->hideOnIndex(),

            FormField::addPanel(''),
            TextField::new('username', $this->trans('admin.ui.name')),
            SlugField::new('slug', $this->trans('admin.ui.slug'))
                ->setTargetFieldName('username')
                ->onlyOnForms(),
            EmailField::new('email', $this->trans('admin.ui.email')),
            // Password hashed with: EventSubscriber/EasyAdminSubscriber
            TextField::new('password')
                ->onlyWhenCreating(),

            FormField::addPanel(''),
            ChoiceField::new('roles', $this->trans('admin.ui.roles'))
                ->setChoices(User::ROLES)
                ->allowMultipleChoices(),
            BooleanField::new('isVerified', $this->trans('admin.ui.verified')),

            FormField::addPanel(''),
            DateTimeField::new('createdAt', $this->trans('admin.ui.created_at'))
                ->onlyOnDetail(),
            DateTimeField::new('updatedAt', $this->trans('admin.ui.updated_at'))
                ->onlyOnDetail(),
            DateTimeField::new('loginAt', $this->trans('admin.ui.login_at'))
                ->onlyOnDetail(),
        ];
    }
}
