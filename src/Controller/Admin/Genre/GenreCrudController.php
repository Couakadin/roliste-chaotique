<?php

namespace App\Controller\Admin\Genre;

use App\Controller\Admin\Utils\TranslatorTrait;
use App\Entity\Genre\Genre;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GenreCrudController extends AbstractCrudController
{
    use TranslatorTrait;

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Genre::class;
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
            ->setPageTitle('new', fn() => $this->trans('admin.new_genre'))
            ->setPageTitle('edit', fn(Genre $genre) => sprintf('Modifier <b>%s</b>', $genre->getName()))
            ->setPageTitle('index', $this->trans('admin.genre', ['%count%' => 2]))
            ->setEntityLabelInPlural($this->trans('admin.genre', ['%count%' => 2]))
            ->setEntityLabelInSingular($this->trans('admin.genre', ['%count%' => 1]));
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
        ];
    }
}
