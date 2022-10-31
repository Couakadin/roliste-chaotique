<?php

namespace App\Controller\Admin\System;

use App\Controller\Admin\Utils\TranslatorTrait;
use App\Entity\System\System;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SystemCrudController extends AbstractCrudController
{
    // $this->trans('');
    use TranslatorTrait;

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return System::class;
    }

    /**
     * @param Crud $crud
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['name'])
            ->setPageTitle('new', fn() => $this->trans('admin.new_system'))
            ->setPageTitle('edit', fn(System $system) => sprintf('Modifier <b>%s</b>', $system->getName()))
            ->setPageTitle('index', $this->trans('admin.system', ['%count%' => 2]))
            ->setEntityLabelInPlural($this->trans('admin.system', ['%count%' => 2]))
            ->setEntityLabelInSingular($this->trans('admin.system', ['%count%' => 1]));
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
            FormField::addTab('Général'),
            IdField::new('id', $this->trans('admin.ui.id'))
                ->hideOnForm(),

            FormField::addPanel(''),
            TextField::new('name', $this->trans('admin.ui.name')),
        ];
    }
}
