<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



/**
 * Class ResetPasswordType
 *
 * @package App\Form
 */
class ChangePasswordType extends AbstractType
{

    /**
     * Builder
     *
     * @param FormBuilderInterface $builder Builder.
     * @param array                $options Array options.
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'disabled' => true,
                'label' => 'Mon adresse email'
            ])
            ->add('firstname', TextType::class, [
                'disabled' => true,
                'label' => 'Mon prénom'
            ])
            ->add('lastname', TextType::class, [
                'disabled' => true,
                'label' => 'Mon nom'
            ])
            ->add('old_password', PasswordType::class, [
                'mapped' => false,        //mapped permet de ne pas lié se champ avec mon Entity (sa évite de le migrate dans la bdd)
                'label' => "Mon mot de passe actuel",
                'attr' => [
                    'placeholder' => "Saisissez votre mot de passe"
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "le mot de passe et la confirmation doivent être identique",
//                'mapped' => false,        //mapped permet de ne pas lié se champ avec mon Entity (sa évite de le migrate dans la bdd)
                'label' => "Votre nouveau mot de passe",
                'required' => true,
                'first_options'=> [
                    'label' => "Votre nouveau mot de passe",
                    'attr' => [
                        'placeholder' => "Veuillez saisir votre nouveau mot de passe"
                    ]
                ],
                'second_options'=>[
                    'label' => "Confirmez votre nouveau mot de passe",
                    'attr' => [
                        'placeholder' => "Veuillez confirmer votre nouveau mot de passe"
                    ]
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label' => "Mettre à jour"
            ]);
    } //fin du buildForm()

    /**
     * Configuration
     *
     * @param OptionsResolver $resolver Resolver.
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }//Fin de configureOptions()

}//Fin de la classe
