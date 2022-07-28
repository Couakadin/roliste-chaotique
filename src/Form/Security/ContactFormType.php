<?php

namespace App\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $data['keySentence'] = strtolower($data['keySentence']);

            $event->setData($data);
        });
        $builder
            ->add('email', EmailType::class, [
                'label'      => 'ui.email',
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'user.email.not_blank',
                        ]
                    ),
                    new Email(
                        [
                            'message' => 'user.email.not_email',
                        ]
                    ),
                ]
            ])
            ->add('subject', TextType::class, [
                'label'      => 'ui.subject',
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'form.subject.not_blank',
                        ]
                    ),
                ]
            ])
            ->add('message', TextareaType::class, [
                'label'      => 'ui.message',
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'form.message.not_blank',
                        ]
                    ),
                ]
            ])
            ->add('keySentence', TextType::class, [
                'mapped'      => false,
                'label'       => 'ui.key_sentence',
                'constraints' => [
                    new EqualTo(
                        [
                            'value'   => 'réussite critique',
                            'message' => 'user.key_sentence.not_equal',
                        ]
                    ),
                    new NotBlank(
                        [
                            'message' => 'user.key_sentence.not_blank',
                        ]
                    )
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}