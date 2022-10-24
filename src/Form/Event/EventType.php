<?php

namespace App\Form\Event;

use App\Entity\Event\Event;
use App\Entity\Table\Table;
use App\Entity\Zone\Zone;
use DateTime;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @property Security $security
 */
class EventType extends AbstractType
{
    public function __construct(){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ->add('bgColor', ColorType::class, [
                'label' => 'ui.bg_color',
            ])
            ->add('borderColor', ColorType::class, [
                'label' => 'ui.border_color',
            ])
            ->add('start', DateTimeType::class, [
                'label'       => 'ui.date_hour_start',
                'widget'      => 'single_text',
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 'today'
                    ])
                ]
            ])
            ->add('end', DateTimeType::class, [
                'label'       => 'ui.date_hour_end',
                'widget'      => 'single_text',
                'constraints' => [
                    new Callback(function ($object, ExecutionContextInterface $context) {
                        $start = $context->getRoot()->getData()->getStart();
                        $end = $object;

                        if (is_a($start, DateTime::class) && is_a($end, DateTime::class)) {
                            if ($end->format('U') - $start->format('U') < 0) {
                                $context
                                    ->buildViolation('form.date.greater')
                                    ->addViolation();
                            }
                        }
                    }),
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label'        => 'ui.type',
                'choices'      => Event::TYPE,
                'choice_label' => function ($choice, $key) {
                    return 'ui.' . $key;
                },
                'constraints'  => [
                    new Choice([
                        'choices' => Event::TYPE,
                        'message' => 'form.event.type'
                    ])
                ]
            ])
            ->add('table', EntityType::class, [
                'label'         => 'ui.table',
                'class'         => Table::class,
                'choice_label'  => 'name',
                'constraints'   => [new Valid()]
            ])
            ->add('zone', EntityType::class, [
                'label'        => 'ui.zone',
                'class'        => Zone::class,
                'choice_label' => 'locality',
                'constraints'  => [new Valid()]
            ])
            ->add('content', CKEditorType::class, [
                'label'        => 'ui.content',
                'constraints'  => [new Valid()]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
