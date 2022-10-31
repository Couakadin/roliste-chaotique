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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AvatarCrudController extends AbstractCrudController
{
    use TranslatorTrait;

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Avatar::class;
    }

    /**
     * @param Crud $crud
     *
     * @return Crud
     */
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

    /**
     * @param Actions $actions
     *
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    /**
     * @param string $pageName
     *
     * @return iterable
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', $this->trans('admin.ui.id'))
                ->hideOnForm(),

            FormField::addPanel(''),
            TextField::new('name', $this->trans('admin.ui.name')),
            ImageField::new('path', $this->trans('admin.ui.image'))
                ->setUploadDir('/public/uploads/images/avatars')
                ->setBasePath('/uploads/images/avatars')
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]'),
        ];
    }
}
