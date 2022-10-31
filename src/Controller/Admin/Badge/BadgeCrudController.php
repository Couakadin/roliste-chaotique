<?php

namespace App\Controller\Admin\Badge;

use App\Controller\Admin\Utils\TranslatorTrait;
use App\Entity\Badge\Badge;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BadgeCrudController extends AbstractCrudController
{
    // $this->trans('');
    use TranslatorTrait;

    /**
     * @return string
     */
    public static function getEntityFqcn(): string
    {
        return Badge::class;
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
            ->setPageTitle('new', fn() => $this->trans('admin.new_badge'))
            ->setPageTitle('edit', fn(Badge $badge) => sprintf('Modifier <b>%s</b>', $badge->getName()))
            ->setPageTitle('index', $this->trans('admin.badge', ['%count%' => 2]))
            ->setEntityLabelInPlural($this->trans('admin.badge', ['%count%' => 2]))
            ->setEntityLabelInSingular($this->trans('admin.badge', ['%count%' => 1]));
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
            IntegerField::new('position', $this->trans('admin.ui.position'))->hideOnForm(),

            FormField::addPanel(''),
            TextField::new('name', $this->trans('admin.ui.name')),
            TextField::new('description', $this->trans('admin.ui.description')),
            TextField::new('actionName', $this->trans('admin.ui.action_name')),
            IntegerField::new('actionCount', $this->trans('admin.ui.action_count')),
        ];
    }
}
