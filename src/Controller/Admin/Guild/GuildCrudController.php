<?php

namespace App\Controller\Admin\Guild;

use App\Controller\Admin\Utils\TranslatorTrait;
use App\Entity\Guild\Guild;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class GuildCrudController extends AbstractCrudController
{
    use TranslatorTrait;

    public static function getEntityFqcn(): string
    {
        return Guild::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['name'])
            ->setPageTitle('new', fn() => $this->trans('admin.new_guild'))
            ->setPageTitle('edit', fn(Guild $guild) => sprintf('Modifier <b>%s</b>', $guild->getName()))
            ->setPageTitle('index', $this->trans('admin.guild', ['%count%' => 2]))
            ->setEntityLabelInPlural($this->trans('admin.guild', ['%count%' => 2]))
            ->setEntityLabelInSingular($this->trans('admin.guild', ['%count%' => 1]))
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
            IdField::new('id', $this->trans('admin.ui.id'))
                ->hideOnForm(),

            FormField::addPanel(''),
            ImageField::new('picture', $this->trans('admin.ui.image'))
                ->setUploadDir('/public/uploads/images/guilds')
                ->setBasePath('/uploads/images/guilds'),
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

            FormField::addPanel(''),
            AssociationField::new('master', $this->trans('admin.ui.guild_master')),
            AssociationField::new('members', $this->trans('admin.ui.guild_members'))
                ->hideOnIndex(),
        ];
    }
}
