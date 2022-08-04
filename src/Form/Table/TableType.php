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
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label'       => 'ui.name',
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'user.name.not_blank',
                        ]
                    ),
                    new Length([
                        // max length allowed by Symfony for security reasons
                        'max'        => 180,
                        'maxMessage' => 'user.name.length'
                    ])
                ],
            ])
            ->add('slug', TextType::class, [
                'label'       => 'ui.slug',
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'user.slug.not_blank',
                        ]
                    ),
                    new Length([
                        // max length allowed by Symfony for security reasons
                        'max'        => 180,
                        'maxMessage' => 'user.slug.length'
                    ])
                ],
            ])
            ->add('content', TextType::class, [
                'label' => 'ui.content',
            ])
            ->add('picture', FileType::class, [
                'required'    => false,
                'label'       => 'ui.picture',
                'data_class'  => null,
                'constraints' => [
                    new File([
                        'maxSize'          => '3000k',
                        'mimeTypes'        => [
                            'image/jpeg',
                            'image/png',
                            'image/svg+xml'
                        ],
                        'mimeTypesMessage' => 'form.file.type',
                        'maxSizeMessage'   => 'form.file.size'
                    ])
                ],
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Table::class,
        ]);
    }
}
