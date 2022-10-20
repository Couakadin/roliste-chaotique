<?php

namespace App\Controller\Admin\Editor;

use App\Controller\Admin\Utils\TranslatorTrait;
use App\Entity\Editor\Editor;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

class EditorCrudController extends AbstractCrudController
{
    use TranslatorTrait;

    public static function getEntityFqcn(): string
    {
        return Editor::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['name'])
            ->setPageTitle('new', fn() => $this->trans('admin.new_editor'))
            ->setPageTitle('edit', fn(Editor $editor) => sprintf('Modifier <b>%s</b>', $editor->getName()))
            ->setPageTitle('index', $this->trans('admin.editor', ['%count%' => 2]))
            ->setEntityLabelInPlural($this->trans('admin.editor', ['%count%' => 2]))
            ->setEntityLabelInSingular($this->trans('admin.editor', ['%count%' => 1]));
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
            TextField::new('name', $this->trans('admin.ui.name')),
            UrlField::new('url', $this->trans('admin.ui.url'))
        ];
    }
}
