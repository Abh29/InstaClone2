<?php

namespace App\Form;

use App\Entity\Profile;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class ,[
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['maxlength' => "250", 'rows' => '5'],
                'required' => false,
            ])
            ->add('url', UrlType::class, [
                'required' => false,
            ])
            ->add('uploadFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'edit-profile-picture',
                    'accept' => 'image/*'],
            ])
            ->add('birthdate', BirthdayType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'profile_item',
        ]);
    }
}
