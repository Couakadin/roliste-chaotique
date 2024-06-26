<?php

namespace App\Controller\Admin\Event;

use App\Controller\Admin\Utils\TranslatorTrait;
use App\Entity\Event\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class EventCrudController extends AbstractCrudController
{
    // $this->trans('');
    use TranslatorTrait;

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Event::class;
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
            ->setPageTitle('new', fn() => $this->trans('admin.new_event'))
            ->setPageTitle('edit', fn(Event $event) => sprintf('Modifier <b>%s</b>', $event->getName()))
            ->setPageTitle('index', $this->trans('admin.event', ['%count%' => 2]))
            ->setEntityLabelInPlural($this->trans('admin.event', ['%count%' => 2]))
            ->setEntityLabelInSingular($this->trans('admin.event', ['%count%' => 1]))
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
            TextField::new('name', $this->trans('admin.ui.name')),
            SlugField::new('slug', $this->trans('admin.ui.slug'))
                ->setTargetFieldName('name')
                ->onlyOnForms(),
            ChoiceField::new('type', 'type')
                ->setChoices(array_flip($this->getTypes())),
            TextField::new('content')->hideOnIndex()->setFormType(CKEditorType::class),
            DateTimeField::new('start', $this->trans('admin.ui.created_at'))
                ->onlyOnDetail(),
            DateTimeField::new('end', $this->trans('admin.ui.updated_at'))
                ->onlyOnDetail(),
            DateTimeField::new('createdAt', $this->trans('admin.ui.created_at'))
                ->onlyOnDetail(),
            DateTimeField::new('updatedAt', $this->trans('admin.ui.updated_at'))
                ->onlyOnDetail(),

            FormField::addTab('Date'),
            DateTimeField::new('start', $this->trans('admin.ui.start_at')),
            DateTimeField::new('end', $this->trans('admin.ui.end_at')),

            FormField::addTab('relation'),
            AssociationField::new('master', $this->trans('admin.ui.master')),
            AssociationField::new('participate', $this->trans('admin.ui.participate'))
                ->hideOnIndex(),
            AssociationField::new('table', $this->trans('admin.ui.table'))
                ->hideOnIndex(),
            AssociationField::new('zone', $this->trans('admin.ui.zone'))
                ->setTemplatePath('@bundles/EasyAdmin/override/zone.html.twig'),
        ];
    }

    /**
     * @return array
     */
    private function getTypes(): array
    {
        $types = [];
        foreach (Event::TYPE as $type) {
            $types[$type] = $this->translator->trans('ui.' . $type);
        }
        return $types;
    }
}
