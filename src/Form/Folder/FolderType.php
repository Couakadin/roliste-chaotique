<?php

namespace App\Form\Folder;

use App\Entity\Folder\Folder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FolderType extends AbstractType
{
    public function __construct(private readonly Security $security) {}

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
            $data->setOwner($this->security->getUser());
        });

        $builder->add('title', TextType::class, [
            'label'       => 'ui.title',
            'constraints' => [
                new Length([
                    'max'        => 64,
                    'maxMessage' => 'form.folder.length'
                ]),
                new NotBlank(['message' => 'form.folder.not_blank',]),
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
            'data_class' => Folder::class,
        ]);
    }
}
