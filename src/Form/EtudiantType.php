<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('RBac', FileType::class, ['mapped' => false])
            ->add('R1', FileType::class, ['mapped' => false])
            ->add('R2', FileType::class, ['mapped' => false])
            ->add('R3', FileType::class, ['mapped' => false])
            ->add('R4', FileType::class, ['mapped' => false])
            ->add('RL1', FileType::class, ['mapped' => false])
            ->add('RL2', FileType::class, ['mapped' => false])
            ->add('RL3', FileType::class, ['mapped' => false])
            ->add('niveauF')
            ->add('niveauA')
            ->add('nomp')
            ->add('prenom')
            ->add('email')
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
