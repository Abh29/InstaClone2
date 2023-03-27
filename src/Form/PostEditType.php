<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\Visibility;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('caption')
            ->add('country')
            ->add('visibility', ChoiceType::class, [
                'choices'  => [
                    'Public' => Visibility::VISIBILITY_PUBLIC,
                    'Private' => Visibility::VISIBILITY_PRIVATE,
                    'Only Followers' => Visibility::VISIBILITY_SUBSCRIBERS_ONLY,
                ],
            ])
            ->add('country')
            ->add('city')
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['maxlength' => "250", 'rows' => '5', 'style' => "resize: none"],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'post_edit_item',
        ]);
    }
}
