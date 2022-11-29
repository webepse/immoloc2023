<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\User;
use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdminBookingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, $this->getConfiguration("Date d'arrivée", "La date à laquelle l'utilisateur arrive",[
                "widget" => "single_text"
            ]))
            ->add('endDate', DateType::class, $this->getConfiguration("Date de départ","La date à laquelle l'utilisateur va partir",[
                "widget" => "single_text"
            ]))
            ->add('comment', TextareaType::class, $this->getConfiguration("Commentaire","Commentaire de l'utilisateur",[
                "required" => false
            ]))
            ->add('booker', EntityType::class, $this->getConfiguration("Locataire",false,[
                'class' => User::class,
                'choice_label' => function($user){
                    return $user->getFirstName()." ".strtoupper($user->getLastName());
                }
            ]))
            ->add('ad', EntityType::class, $this->getConfiguration("Annonce",false,[
                'class' => Ad::class,
                'choice_label' => "title"
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
