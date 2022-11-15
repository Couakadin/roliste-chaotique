<?php

namespace App\Form\Event;

use App\Entity\Event\Event;
use App\Entity\Table\Table;
use App\Entity\Zone\Zone;
use DateTimeImmutable;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @property Security $security
 */
class EventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            $data->getEnd() ?: $data->setEnd($data->getStart());

            $newDateStart = DateTimeImmutable::createFromFormat('Y-m-d H:i', $data->getStart()
                    ->format('Y-m-d') . ' ' . $form['startHour']->getData()->format('H:i'));
            $data->setStart($newDateStart);

            $newDateEnd = DateTimeImmutable::createFromFormat('Y-m-d H:i', $data->getEnd()
                    ->format('Y-m-d') . ' ' . $form['endHour']->getData()->format('H:i'));
            $data->setEnd($newDateEnd);

            if ($newDateEnd->format('Y-m-d H:i') < $newDateStart->format('Y-m-d H:i')) {
                $form->get('start')->addError(new FormError('la date de fin doit être supérieure ou égale à la date de départ.'));
            }
        });

        $builder
            ->add('name', TextType::class, [
                'label'       => 'ui.name',
            ])
            ->add('totalParticipate', IntegerType::class, [
                'label'       => 'ui.total_participate',
                'required'    => false,
            ])
            ->add('initiation', CheckboxType::class, [
                'required' => false,
                'label_attr'  => [
                    'class' => 'checkbox-label',
                ],
                'attr'        => [
                    'class' => 'checkbox',
                ],
            ])
            ->add('start', DateType::class, [
                'input'       => 'datetime_immutable',
                'label'       => 'ui.date_start',
                'widget'      => 'single_text',
            ])
            ->add('end', DateType::class, [
                'input'    => 'datetime_immutable',
                'required' => false,
                'label'    => 'ui.date_end',
                'widget'   => 'single_text',
            ])
            ->add('startHour', TimeType::class, [
                'mapped'      => false,
                'label'       => 'ui.date_hour_start',
                'widget'      => 'single_text',
                'constraints' => [
                    new NotNull(['message' => 'entity.not_blank']),
                    new NotBlank(['message' => 'entity.not_blank']),
                ]
            ])
            ->add('endHour', TimeType::class, [
                'mapped'      => false,
                'label'       => 'ui.date_hour_end',
                'widget'      => 'single_text',
                'constraints' => [
                    new NotNull(['message' => 'entity.not_blank']),
                    new NotBlank(['message' => 'entity.not_blank']),
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label'        => 'ui.type',
                'choices'      => Event::TYPE,
                'choice_label' => function ($choice, $key) {
                    return 'ui.' . $key;
                },
            ])
            ->add('table', EntityType::class, [
                'label'         => 'ui.table',
                'class'         => Table::class,
                'choice_label'  => 'name',
            ])
            ->add('zone', EntityType::class, [
                'required'     => false,
                'empty_data'   => null,
                'label'        => 'ui.zone',
                'class'        => Zone::class,
                'choice_label' => 'locality',
            ])
            ->add('content', CKEditorType::class, [
                'label'        => 'ui.content',
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
            'data_class' => Event::class,
        ]);
    }
}
