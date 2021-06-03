<?php

namespace App\Form;

use App\Entity\Player;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TournamentPlayersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        for ($i = 0; $i < $options['nbJoueurs']; $i++) {
            $j = $i + 1;
            $builder
                ->add('player' . $j, EntityType::class, [
                    'label' => 'Joueur ' . $j,
                    'class' => Player::class,
                    'choice_label' => function ($player) {
                        return $player->getFullName();
                    }]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'nbJoueurs' => 0,
        ]);
    }
}
