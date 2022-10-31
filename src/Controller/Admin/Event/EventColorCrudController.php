<?php

namespace App\Controller\Admin\Event;

use App\Controller\Admin\Utils\TranslatorTrait;
use App\Entity\Event\EventColor;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class EventColorCrudController extends AbstractCrudController
{
    // $this->trans('');
    use TranslatorTrait;

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return EventColor::class;
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
            ->setPageTitle('new', fn() => $this->trans('admin.new_event_color'))
            ->setPageTitle('edit', fn(EventColor $eventColor) => sprintf('Modifier couleur <b>%s</b>', $eventColor->getTable()))
            ->setPageTitle('index', $this->trans('admin.event_color', ['%count%' => 2]))
            ->setEntityLabelInPlural($this->trans('admin.event_color', ['%count%' => 2]))
            ->setEntityLabelInSingular($this->trans('admin.event_color', ['%count%' => 1]))
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
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

            AssociationField::new('table', $this->trans('admin.ui.table')),
            ColorField::new('bgColor', $this->trans('admin.ui.bg_color')),
            ColorField::new('borderColor', $this->trans('admin.ui.border_color')),
        ];
    }
}
