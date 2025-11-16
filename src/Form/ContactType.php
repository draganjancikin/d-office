<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', null, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'id' => 'selectType',
                    'title' => 'Izaberite vrstu kontakta',
                    'class' => 'form-select form-control',
                ],
            ])
            ->add('body', null, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'id' => 'inputBody',
                    'title' => 'Unesite kontakt',
                    'placeholder' => 'Unesite kontakt',
                    'class' => 'form-control',
                ],
            ])
            ->add('note', null, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'id' => 'inputNody',
                    'title' => 'Unesite napomenu uz kontakt',
                    'placeholder' => 'Unesite napomenu uz kontakt',
                    'class' => 'form-control',
                ],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }

}

