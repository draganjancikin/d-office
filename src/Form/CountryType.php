<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Country;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Naziv',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Unesite naziv drzave',
                    'class' => 'form-control form-control-sm',
                ],
            ])
            ->add('abbr', TextType::class, [
                'label' => 'Oznaka',
                'required' => false,
                'attr' => [
                    'title' => 'Oznaka zemlje',
                    'placeholder' => 'Unesite oznaku',
                    'class' => 'form-control form-control-sm',
                    'maxlength' => 3,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
        ]);
    }
}
