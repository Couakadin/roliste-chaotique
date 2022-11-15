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
use Symfony\Component\Validator\Constraints\Valid;

class UserType extends AbstractType
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
            ->add('email', EmailType::class, [
                'label' => 'ui.email',
            ])
            ->add('username', TextType::class, [
                'label' => 'ui.username',
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'label' => 'ui.slug',
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => User::ROLES,
                'multiple' => true
            ])
            ->add('password', PasswordType::class, [
                'label' => 'ui.password',
            ])
            ->add('isVerified', CheckboxType::class)
            ->add('createdAt', DateTimeType::class)
            ->add('updatedAt', DateTimeType::class)
            ->add('loggedAt', DateTimeType::class)
            ->add('avatar', EntityType::class, [
                'class' => Avatar::class,
                'choice_label' => 'path',
                'constraints' => [new Valid()]
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
            'data_class' => User::class,
        ]);
    }
}
