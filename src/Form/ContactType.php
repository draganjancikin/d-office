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
                'label' => 'Tip kontakta',
                'label_attr' => [
                    'for' => 'selectType',
                    'class' => 'col-sm-3 col-form-label text-left text-sm-right',
                ],
                'required' => true,
                'attr' => [
                    'id' => 'selectType',
                    'title' => 'Izaberite vrstu kontakta',
                    'class' => 'form-select form-control',
                ],
            ])
            ->add('body', null, [
                'label' => 'Kontakt',
                'label_attr' => [
                    'for' => 'selectType',
                    'class' => 'col-sm-3 col-form-label text-left text-sm-right',
                ],
                'required' => true,
                'attr' => [
                    'id' => 'inputBody',
                    'title' => 'Unesite kontakt',
                    'placeholder' => 'Unesite kontakt',
                    'class' => 'form-control',
                ],
            ])
            ->add('note', null, [
                'label' => 'Beleška',
                'label_attr' => [
                    'for' => 'inputNote',
                    'class' => 'col-sm-3 col-form-label text-left text-sm-right',
                ],
                'required' => false,
                'attr' => [
                    'id' => 'inputNote',
                    'title' => 'Unesite napomenu uz kontakt',
                    'placeholder' => 'Unesite napomenu uz kontakt',
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }

}

