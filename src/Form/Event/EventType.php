<?php

namespace App\Form\Event;

use App\Entity\Event\Event;
use App\Entity\Table\Table;
use App\Entity\Zone\Zone;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @property Security $security
 */
class EventType extends AbstractType
{
    public function __construct(private readonly Security $security){}

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
            ->add('slug', TextType::class, [
                'label'       => 'ui.slug',
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'user.slug.not_blank',
                        ]
                    ),
                    new Length([
                        // max length allowed by Symfony for security reasons
                        'max'        => 180,
                        'maxMessage' => 'user.slug.length'
                    ])
                ],
            ])
            ->add('bgColor', ColorType::class)
            ->add('borderColor', ColorType::class)
            ->add('start', DateTimeType::class)
            ->add('end', DateTimeType::class)
            ->add('createdAt', DateTimeType::class)
            ->add('updatedAt', DateTimeType::class)
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
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('t')
                        ->where(':user MEMBER OF t.members')
                        ->setParameter('user', $this->security->getUser());
                },
                'constraints'   => [new Valid()]
            ])
            ->add('zone', EntityType::class, [
                'label'        => 'ui.zone',
                'class'        => Zone::class,
                'choice_label' => 'locality',
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
