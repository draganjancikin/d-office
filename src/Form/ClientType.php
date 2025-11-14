<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Client;
use App\Entity\ClientType as ClientTypeEntity;
use App\Entity\Contact;
use App\Entity\Country;
use App\Entity\Street;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EntityType::class, [
                'label' => 'Vrsta klijenta',
                'label_attr' => [
                    'for' => 'selectTip',
                    'class' => 'col-sm-3 col-form-label text-left text-sm-right',
                ],
                'attr' => [
                    'id' => 'selectTip',
                    'title' => 'Izaberite vrstu klijenta',
                    'class' => 'form-select form-select-sm',
                ],
                'class' => ClientTypeEntity::class,
                'choice_label' => 'name', // show type name instead of id
            ])
            ->add('name', null, [
                'label' => 'Naziv klijenta',
                'label_attr' => [
                    'for' => 'inputName',
                    'class' => 'col-sm-3 col-form-label text-left text-sm-right',
                ],
                'attr' => [
                    'id' => 'inputName',
                    'title' => 'Unesite naziv klijenta',
                    'placeholder' => 'Unesite naziv klijenta',
                    'class' => 'form-control form-control-sm',
                ],
            ])
            ->add('name_note', null, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'title' => 'Unsetite napomenu uz naziv klijenta',
                    'placeholder' => 'Unesite napomenu uz naziv klijenta',
                    'class' => 'form-control form-control-sm',
                ],
            ])
            ->add('lb', null, [
                'required' => false,
                'label' => 'PIB klijenta',
                'label_attr' => [
                    'for' => 'inputLb',
                    'class' => 'col-sm-3 col-form-label text-left text-sm-right',
                ],
                'attr' => [
                    'id' => 'inputLb',
                    'title' => 'Unesite PIB klijenta ako je klijent pravno lice',
                    'placeholder' => 'Za klijente pravna lica',
                    'class' => 'form-control form-control-sm',
                    'maxlength' => 9,
                ],
            ])
            ->add('is_supplier', null, [
                'required' => false,
                'label' => 'Dobavljač',
                'label_attr' => [
                    'for' => 'inputIsSupplier',
                    'class' => 'form-check-label col-sm-3 col-form-label text-left text-sm-right',
                ],
                'attr' => [
                    'id' => 'inputIsSupplier',
                    'title' => 'Ako je označeno, klijent je i dobavljač',
                    'class' => 'form-check-input ml-0',
                ],
            ])
            ->add('country', EntityType::class, [
                'required' => false,
                'class' => Country::class,
                'choice_label' => 'name',
                'label' => 'Država',
                'label_attr' => [
                    'for' => 'selectCountry',
                    'class' => 'col-sm-3 col-form-label text-left text-sm-right',
                ],
                'attr' => [
                    'id' => 'selectCountry',
                    'title' => 'Izaberite Državu',
                    'class' => 'form-select form-select-sm',
                ],
                'placeholder' => 'Izaberite državu',
            ])
            ->add('city', EntityType::class, [
                'required' => false,
                'class' => City::class,
                'choice_label' => 'name',
                'label' => 'Naseljeno mesto',
                'label_attr' => [
                    'for' => 'selectCity',
                    'class' => 'col-sm-3 col-form-label text-left text-sm-right',
                ],
                'attr' => [
                    'id' => 'selectCity',
                    'title' => 'Izaberite Grad',
                    'class' => 'form-select form-select-sm',
                ],
                'placeholder' => 'Izaberite grad',
            ])
            ->add('street', EntityType::class, [
                'required' => false,
                'class' => Street::class,
                'choice_label' => 'name',
                'label' => 'Ulica',
                'label_attr' => [
                    'for' => 'selectStreet',
                    'class' => 'col-sm-3 col-form-label text-left text-sm-right',
                ],
                'attr' => [
                    'id' => 'selectStreet',
                    'title' => 'Izaberite Ulicu',
                    'class' => 'form-select form-select-sm',
                ],
                'placeholder' => 'Izaberite ulicu',
            ])
            ->add('home_number', null, [
                'required' => false,
                'label' => 'Broj kuće',
                'label_attr' => [
                    'for' => 'InputHomeNumber',
                    'class' => 'col-sm-3 col-form-label text-left text-sm-right',
                ],
                'attr' => [
                    'id' => 'InputHomeNumber',
                    'title' => 'Unesite broj kuće',
                    'class' => 'form-control form-control-sm',
                ],
            ])
            ->add('address_note', null, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'id' => 'ImputHomeNumber',
                    'title' => 'Unesite napomenu uz adresu',
                    'class' => 'form-control form-control-sm',
                    'placeholder' => 'Unesite napomenu uz adresu',
                ],
            ])
            ->add('note', null, [
                'required' => false,
                'label' => 'Beleška o klijentu',
                'label_attr' => [
                    'for' => 'formInputNote',
                    'class' => 'col-sm-3 col-form-label text-left text-sm-right',
                ],
                'attr' => [
                    'id' => 'InputNote',
                    'title' => 'Unesite napomenu uz klijenta',
                    'class' => 'form-control form-control-sm',
                    'rows' => 3,
                ],
            ])


//            ->add('contacts', EntityType::class, [
//                'class' => Contact::class,
//                'choice_label' => 'id',
//                'multiple' => true,
//            ])
//            ->add('created_by_user', EntityType::class, [
//                'class' => User::class,
//                'choice_label' => 'id',
//            ])
//            ->add('modified_by_user', EntityType::class, [
//                'class' => User::class,
//                'choice_label' => 'id',
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
