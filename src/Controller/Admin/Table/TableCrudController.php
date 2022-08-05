<?php

namespace App\Controller\Admin\Table;

use App\Controller\Admin\Utils\TranslatorTrait;
use App\Entity\Table\Table;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TableCrudController extends AbstractCrudController
{
    // $this->trans('');
    use TranslatorTrait;

    public static function getEntityFqcn(): string
    {
        return Table::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['name'])
            ->setPageTitle('new', fn() => $this->trans('admin.new_table'))
            ->setPageTitle('edit', fn(Table $table) => sprintf('Modifier <b>%s</b>', $table->getName()))
            ->setPageTitle('index', $this->trans('admin.table', ['%count%' => 2]))
            ->setEntityLabelInPlural($this->trans('admin.table', ['%count%' => 2]))
            ->setEntityLabelInSingular($this->trans('admin.table', ['%count%' => 1]))
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Général'),
            IdField::new('id', $this->trans('admin.ui.id'))
                ->hideOnForm(),
            BooleanField::new('showcase', $this->trans('admin.ui.showcase')),
            ImageField::new('picture', $this->trans('admin.ui.image'))
                ->setUploadDir('/public/uploads/images/tables')
                ->setBasePath('/uploads/images/tables'),
            TextField::new('name', $this->trans('admin.ui.name')),
            SlugField::new('slug', $this->trans('admin.ui.slug'))
                ->setTargetFieldName('name')
                ->onlyOnForms(),
            TextEditorField::new('content', $this->trans('admin.ui.content'))
                ->setFormType(CKEditorType::class)
                ->onlyOnForms(),
            FormField::addPanel(''),
            DateTimeField::new('createdAt', $this->trans('admin.ui.created_at'))
                ->onlyOnDetail(),
            DateTimeField::new('updatedAt', $this->trans('admin.ui.updated_at'))
                ->onlyOnDetail(),

            FormField::addTab('Relations'),
            AssociationField::new('members', $this->trans('admin.ui.members')),
            AssociationField::new('events', $this->trans('admin.ui.events'))
                ->hideOnIndex(),
        ];
    }
}
