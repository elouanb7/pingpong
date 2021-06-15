<?php

namespace App\Form;

use App\Entity\Player;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoosePlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder
                ->add('player', EntityType::class, [
                    'label' => 'Joueur',
                    'class' => Player::class,
                    'choice_label' => function ($player) {
                        return $player->getFullName();
                    }]);
        }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}