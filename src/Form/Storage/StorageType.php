<?php

namespace App\Form\Storage;

use App\Entity\Folder\Folder;
use App\Entity\Storage\Storage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\UX\Dropzone\Form\DropzoneType;

class StorageType extends AbstractType
{
    public function __construct(private readonly Security $security) { }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $data->setUser($this->security->getUser());
        });

        $builder
            ->add('originalName', TextType::class, [
                'label' => 'ui.original_name'
            ])
            ->add('folder', EntityType::class, [
                'class'        => Folder::class,
                'choice_label' => 'title',
                'required'     => false,
                'label'        => 'ui.storage'
            ])
            ->add('imageFile', DropzoneType::class, [
                'label'       => false,
                'attr'        => [
                    'placeholder' => 'ui.drag_drop',
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
            'data_class'         => Storage::class,
            "allow_extra_fields" => true,
        ]);
    }
}
