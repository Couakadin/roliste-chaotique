<?php

namespace App\Form\Security;

use App\Entity\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $data['keySentence'] = strtolower($data['keySentence']);

            $event->setData($data);
        });

        $builder
            ->add('keySentence', TextType::class, [
                'mapped'      => false,
                'label'       => 'ui.key_sentence',
                'constraints' => [
                    new EqualTo(
                        [
                            'value'   => 'le maître du jeu a toujours raison',
                            'message' => 'entity.equal_to',
                        ]
                    ),
                    new NotBlank(
                        [
                            'message' => 'entity.not_blank',
                        ]
                    )
                ],
            ])
            ->add('email', EmailType::class, [
                'label'       => 'ui.email',
            ])
            ->add('username', TextType::class, [
                'label'       => 'ui.username',
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped'      => false,
                'label'       => 'ui.agree_terms',
                'label_attr'  => [
                    'class' => 'checkbox-label',
                ],
                'attr'        => [
                    'class' => 'checkbox',
                ],
                'constraints' => [
                    new IsTrue(['message' => 'entity.is_true',]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped'      => false,
                'label'       => 'ui.password',
                'attr'        => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(['message' => 'entity.not_blank',]),
                    new Length([
                        'min'        => 6,
                        'minMessage' => 'entity.length.min',
                        // max length allowed by Symfony for security reasons
                        'max'        => 4096,
                        'maxMessage' => 'entity.length.max',
                    ]),
                ],
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
