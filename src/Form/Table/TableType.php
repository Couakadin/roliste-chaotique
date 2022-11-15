<?php

namespace App\Form\Table;

use App\Entity\Table\Table;
use App\Entity\User\User;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TableType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label'       => 'ui.name',
            ])
            ->add('slug', TextType::class, [
                'label'       => 'ui.slug',
            ])
            ->add('content', TextType::class, [
                'label' => 'ui.content',
            ])
            ->add('picture', FileType::class, [
                'required'    => false,
                'label'       => 'ui.picture',
                'data_class'  => null,
            ])
            ->add('showcase', CheckboxType::class)
            ->add('createdAt', DateTimeType::class)
            ->add('updatedAt', DateTimeType::class)
            ->add('master', EntityType::class, [
                'required'     => false,
                'class'        => User::class,
                'choice_label' => 'username',
            ])
            ->add('members', EntityType::class, [
                'required'     => false,
                'class'        => User::class,
                'choice_label' => 'username',
                'multiple'     => true,
                'expanded'     => true
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Table::class,
        ]);
    }
}
