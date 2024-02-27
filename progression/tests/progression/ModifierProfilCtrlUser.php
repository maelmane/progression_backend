<?php

use PHPUnit\Framework\TestCase;
use progression\presentation\controller\ModifierProfilCtl;
use progression\domaine\entité\user\User;

class ModifierProfilCtlTest extends TestCase
{
    public function test_étant_donné_un_utilisateur_existant_lorsque_lutilisateur_met_à_jour_son_adresse_courriel_a_nouveau_example_com_alors_ladresse_courriel_de_lutilisateur_devrait_être_nouveau_example_com()
    {
        $user = new User();
        $user->setCourriel("ancien@example.com");

        $controller = new ModifierProfilCtl();
        $controller->modifierCourriel($user, "nouveau@example.com");

        $this->assertEquals("nouveau@example.com", $user->getCourriel());
    }

    public function test_étant_donné_un_utilisateur_existant_avec_le_pseudo_ancienPseudo_lorsque_lutilisateur_met_à_jour_son_pseudo_a_nouveauPseudo_alors_le_pseudo_de_lutilisateur_devrait_être_nouveauPseudo()
    {
        $user = new User();
        $user->setPseudo("ancienPseudo");

        $controller = new ModifierProfilCtl();
        $controller->modifierPseudo($user, "nouveauPseudo");

        $this->assertEquals("nouveauPseudo", $user->getPseudo());
    }

    public function test_étant_donné_un_utilisateur_existant_avec_une_ancienne_biographie_lorsque_lutilisateur_met_à_jour_sa_biographie_alors_la_biographie_de_lutilisateur_devrait_être_nouvelle_biographie()
    {
        $user = new User();
        $user->setBiographie("Ancienne biographie");

        $controller = new ModifierProfilCtl();
        $controller->modifierBiographie($user, "Nouvelle biographie");

        $this->assertEquals("Nouvelle biographie", $user->getBiographie());
    }

    public function test_étant_donné_un_utilisateur_existant_avec_un_ancien_avatar_lorsque_lutilisateur_met_à_jour_son_avartar_a_nouvelAvatar_jpg_alors_lavatar_de_lutilisateur_devrait_être_nouvelAvatar_jpg()
    {
        $user = new User();
        $user->setAvatar("ancienAvatar.jpg");

        $controller = new ModifierProfilCtl();
        $controller->modifierAvatar($user, "nouvelAvatar.jpg");

        $this->assertEquals("nouvelAvatar.jpg", $user->getAvatar());
    }
}
