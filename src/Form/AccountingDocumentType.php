<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\AccountingDocument;
use App\Entity\AccountingDocumentType as AccountingDocumentTypeEntity;
use App\Entity\Client;
use App\Entity\Project;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountingDocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', EntityType::class, [
                'label' => 'Vrsta dokumenta',
                'label_attr' => [
                    'for' => 'selectType',
                    'class' => 'col-sm-4 col-md-3 col-xl-2 col-form-label text-left text-sm-right',
                ],
                'attr' => [
                    'id' => 'selectType',
                    'title' => 'Izaberite vrstu dokumenta',
                    'class' => 'form-select form-select-sm',
                ],
                'class' => AccountingDocumentTypeEntity::class,
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('client', EntityType::class, [
                'label' => 'Klijent',
                'label_attr' => [
                    'for' => 'selectClient',
                    'class' => 'col-sm-4 col-md-3 col-xl-2 col-form-label text-left text-sm-right',
                ],
                'attr' => [
                    'id' => 'selectClient',
                    'title' => 'Izaberite klijenta',
                    'class' => 'form-select form-select-sm',
                ],
                'class' => Client::class,
                'choice_label' => 'name',
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC'); // A–Z
                },
            ])
            ->add('title', null, [
                'label' => 'Naslov',
                'label_attr' => [
                    'for' => 'inputTitle',
                    'class' => 'col-sm-4 col-md-3 col-xl-2 col-form-label text-left text-sm-right',
                ],
                'attr' => [
                    'id' => 'inputTitle',
                    'placeholder' => 'Unesite naslov dokumenta',
                    'class' => 'form-control form-control-sm',
                ],
            ])
            ->add('note', null, [
                'required' => false,
                'label' => 'Beleška',
                'label_attr' => [
                    'for' => 'inputNote',
                    'class' => 'col-sm-4 col-md-3 col-xl-2 col-form-label text-left text-sm-right',
                ],
                'attr' => [
                    'id' => 'inputNote',
                    'placeholder' => 'Unesite belešku uz dokument',
                    'class' => 'form-control form-control-sm',
                    'rows' => 3,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AccountingDocument::class,
        ]);
    }
}
