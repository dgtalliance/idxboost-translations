<?php

namespace App\Form;

use App\Entity\Language;
use App\Entity\Term;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class LoadTermsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('language', EntityType::class, [
                'class' => Language::class,
                'required' => true
            ])
            ->add('translationFile', FileType::class,array(
                "attr" =>array("type" => "file" ),
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'text/x-po',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid translation file(.po).',
                    ])
                ],
                "required" => true,

            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
