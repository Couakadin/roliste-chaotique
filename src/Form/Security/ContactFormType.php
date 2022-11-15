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
            ->add('email', EmailType::class, [
                'label'      => 'ui.email',
                'constraints' => [
                    new NotBlank(['message' => 'entity.not_blank']),
                    new Email(['message' => 'entity.email'])
                ]
            ])
            ->add('subject', TextType::class, [
                'label'      => 'ui.subject',
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'entity.not_blank',
                        ]
                    ),
                ]
            ])
            ->add('message', TextareaType::class, [
                'label'      => 'ui.message',
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'entity.not_blank',
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
                            'message' => 'entity.equal_to',
                        ]
                    ),
                    new NotBlank(
                        [
                            'message' => 'entity.not_blank',
                        ]
                    )
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
            'data_class' => null
        ]);
    }
}
