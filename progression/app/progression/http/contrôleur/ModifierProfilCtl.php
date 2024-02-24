<?php

namespace progression\presentation\controller;

use progression\domaine\entité\user\User;
use InvalidArgumentException;

class ModifierProfilCtl
{
    public function modifierCourriel(User $user, string $nouveauCourriel): void
    {
        try {
            $user->setCourriel($nouveauCourriel);
            echo "L'adresse courriel a été modifiée avec succès.";
        } catch (InvalidArgumentException $e) {
            $this->gererErreur($e);
        }
    }

    public function modifierPseudo(User $user, string $nouveauPseudo): void
    {
        try {
            $user->setPseudo($nouveauPseudo);
            echo "Le pseudo a été modifié avec succès.";
        } catch (InvalidArgumentException $e) {
            $this->gererErreur($e);
        }
    }

    public function modifierBiographie(User $user, string $nouvelleBiographie): void
    {
        try {
            $user->setBiographie($nouvelleBiographie);
            echo "La biographie a été modifiée avec succès.";
        } catch (InvalidArgumentException $e) {
            $this->gererErreur($e);
        }
    }

    public function modifierAvatar(User $user, string $nouvelAvatar): void
    {
        try {
            $user->setAvatar($nouvelAvatar);
            echo "L'avatar a été modifié avec succès.";
        } catch (InvalidArgumentException $e) {
            $this->gererErreur($e);
        }
    }

    private function gererErreur(InvalidArgumentException $e): void
    {
        echo "<div style='color: red;'>Erreur: " . $e->getMessage() . "</div>";
    }
}
