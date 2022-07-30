<?php

namespace App\Form\Guild;

use App\Entity\Game\Game;
use App\Entity\Guild\Guild;
use App\Entity\User\User;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class GuildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                $data['totalGames'] = isset($data['games']) ? count($data['games']) : 0;

                $event->setData($data);
            })
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
            ->add('games', EntityType::class, [
                'required'     => false,
                'class'        => Game::class,
                'choice_label' => 'name',
                'multiple'     => true,
                'expanded'     => true,
            ])
            ->add('members', EntityType::class, [
                'required'     => false,
                'class'        => User::class,
                'choice_label' => 'username',
                'multiple'     => true,
                'expanded'     => true
            ])
            ->add('totalGames', IntegerType::class, [
                'mapped' => false,
                'constraints' => [
                    new LessThanOrEqual(
                        [
                            'message' => 'form.guild.number_game',
                            'value' => 5
                        ]
                    )
                ],
            ])
            ->add('content', CKEditorType::class, [
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
            ->add('createdAt', DateTimeType::class)
            ->add('updatedAt', DateTimeType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Guild::class,
        ]);
    }
}
