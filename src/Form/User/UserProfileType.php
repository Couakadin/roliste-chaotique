<?php

namespace App\Form\User;

use App\Entity\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserProfileType extends AbstractType
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
            ;
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
