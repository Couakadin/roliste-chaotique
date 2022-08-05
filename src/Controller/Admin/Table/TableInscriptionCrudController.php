<?php

namespace App\Controller\Admin\Table;

use App\Controller\Admin\Utils\TranslatorTrait;
use App\Entity\Table\Table;
use App\Entity\Table\TableInscription;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TableInscriptionCrudController extends AbstractCrudController
{
    // $this->trans('');
    use TranslatorTrait;

    public static function getEntityFqcn(): string
    {
        return TableInscription::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['name'])
            ->setPageTitle('new', fn() => $this->trans('admin.new_table_inscription'))
            ->setPageTitle('edit', fn(TableInscription $tableInscription) => sprintf('Modifier <b>%s</b>', $tableInscription->getId()))
            ->setPageTitle('index', $this->trans('admin.table_inscription', ['%count%' => 2]))
            ->setEntityLabelInPlural($this->trans('admin.table_inscription', ['%count%' => 2]))
            ->setEntityLabelInSingular($this->trans('admin.table_inscription', ['%count%' => 1]))
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

            FormField::addPanel(''),
            ChoiceField::new('status', $this->trans('admin.ui.status'))
                ->setChoices(TableInscription::STATUS),
            DateTimeField::new('createdAt', $this->trans('admin.ui.created_at'))
                ->onlyOnDetail(),
            DateTimeField::new('updatedAt', $this->trans('admin.ui.updated_at'))
                ->onlyOnDetail(),

            FormField::addTab('Relations'),
            AssociationField::new('user', $this->trans('admin.user', ['%count%' => 1])),
            AssociationField::new('table', $this->trans('admin.table', ['%count%' => 1]))
        ];
    }
}
