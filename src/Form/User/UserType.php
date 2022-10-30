<?php

namespace App\Form\User;

use App\Entity\Avatar\Avatar;
use App\Entity\User\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label'       => 'ui.email',
                'constraints' => [
                    new NotBlank(['message' => 'user.email.not_blank',]),
                    new Email(['message' => 'user.email.not_email',]),
                    new Length([
                        // max length allowed by Symfony for security reasons
                        'max'        => 180,
                        'maxMessage' => 'user.email.length'
                    ])
                ],
            ])
            ->add('username', TextType::class, [
                'label'       => 'ui.username',
                'constraints' => [
                    new NotBlank(['message' => 'user.username.not_blank',]),
                    new Length([
                        // max length allowed by Symfony for security reasons
                        'max'        => 180,
                        'maxMessage' => 'user.username.length'
                    ])
                ],
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'label'       => 'ui.slug',
                'constraints' => [
                    new NotBlank(['message' => 'user.slug.not_blank',]),
                    new Length([
                        // max length allowed by Symfony for security reasons
                        'max'        => 180,
                        'maxMessage' => 'user.slug.length'
                    ])
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'choices'  => User::ROLES,
                'multiple' => true
            ])
            ->add('password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label'       => 'ui.password',
                'constraints' => [
                    new NotBlank(['message' => 'user.password.not_blank',]),
                    new Length([
                        'min'        => 6,
                        'minMessage' => 'user.password.length',
                        // max length allowed by Symfony for security reasons
                        'max'        => 4096,
                    ]),
                ],
            ])
            ->add('isVerified', CheckboxType::class)
            ->add('createdAt', DateTimeType::class)
            ->add('updatedAt', DateTimeType::class)
            ->add('loggedAt', DateTimeType::class)
            ->add('avatar', EntityType::class, [
                'class'        => Avatar::class,
                'choice_label' => 'path',
                'constraints'  => [new Valid()]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
