<?php

namespace App\Controller\Admin\Event;

use App\Controller\Admin\Utils\TranslatorTrait;
use App\Entity\Event\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EventCrudController extends AbstractCrudController
{
    // $this->trans('');
    use TranslatorTrait;

    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['name'])
            ->setPageTitle('new', fn() => $this->trans('admin.new_event'))
            ->setPageTitle('edit', fn(Event $event) => sprintf('Modifier <b>%s</b>', $event->getName()))
            ->setPageTitle('index', $this->trans('admin.event', ['%count%' => 2]))
            ->setEntityLabelInPlural($this->trans('admin.event', ['%count%' => 2]))
            ->setEntityLabelInSingular($this->trans('admin.event', ['%count%' => 1]))
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
            TextField::new('name', $this->trans('admin.ui.name')),
            SlugField::new('slug', $this->trans('admin.ui.slug'))
                ->setTargetFieldName('name')
                ->onlyOnForms(),
            ColorField::new('bgColor', $this->trans('admin.ui.bg_color'))->hideOnIndex(),
            ColorField::new('borderColor', $this->trans('admin.ui.border_color'))->hideOnIndex(),


            FormField::addPanel(''),
            DateTimeField::new('start', $this->trans('admin.ui.created_at'))
                ->onlyOnDetail(),
            DateTimeField::new('end', $this->trans('admin.ui.updated_at'))
                ->onlyOnDetail(),
            DateTimeField::new('createdAt', $this->trans('admin.ui.created_at'))
                ->onlyOnDetail(),
            DateTimeField::new('updatedAt', $this->trans('admin.ui.updated_at'))
                ->onlyOnDetail(),
        ];
    }
}
