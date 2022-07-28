<?php

namespace App\Controller\Admin\Avatar;

use App\Controller\Admin\Utils\TranslatorTrait;
use App\Entity\Avatar\Avatar;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class AvatarCrudController extends AbstractCrudController
{
    use TranslatorTrait;

    public static function getEntityFqcn(): string
    {
        return Avatar::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['path'])
            ->setPageTitle('new', fn() => $this->trans('admin.new_avatar'))
            ->setPageTitle('edit', fn(Avatar $avatar) => sprintf('Modifier <b>%s</b>', $avatar->getPath()))
            ->setPageTitle('index', $this->trans('admin.avatar', ['%count%' => 2]))
            ->setEntityLabelInPlural($this->trans('admin.avatar', ['%count%' => 2]))
            ->setEntityLabelInSingular($this->trans('admin.avatar', ['%count%' => 1]));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', $this->trans('admin.ui.id'))
                ->hideOnForm(),

            FormField::addPanel(''),
            ImageField::new('path', $this->trans('admin.ui.image'))
                ->setUploadDir('/public/uploads/images/avatars')
                ->setBasePath('/uploads/images/avatars'),
        ];
    }
}
