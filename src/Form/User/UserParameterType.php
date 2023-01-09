<?php

namespace App\Form\User;

use App\Entity\User\UserParameter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserParameterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('eventEmailReminder', CheckboxType::class, [
                'required' => false,
                'label'       => 'ui.event_email_reminder',
                'label_attr'  => [
                    'class' => 'checkbox-label',
                ],
                'attr'        => [
                    'class' => 'checkbox',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserParameter::class,
        ]);
    }
}
