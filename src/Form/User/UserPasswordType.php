<?php

namespace App\Form\User;

use App\Entity\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserPasswordType extends AbstractType
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
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            ->add('password', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'label' => 'ui.password',
                'invalid_message' => 'entity.not_same',
                'required' => true,
                'first_options' => ['label' => 'ui.password'],
                'second_options' => ['label' => 'ui.repeat_password'],
                'constraints' => [
                    new NotBlank(['message' => 'entity.not_blank']),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'entity.length.min',
                        'max' => 4096,
                        'maxMessage' => 'entity.length.max',
                    ])
                ]
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
