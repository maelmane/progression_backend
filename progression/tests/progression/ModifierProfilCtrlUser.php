<?php

use PHPUnit\Framework\TestCase;
use progression\presentation\controller\ModifierProfilCtl;
use progression\domaine\entité\user\User;

class ModifierProfilCtlTest extends TestCase
{
    public function testModifierCourriel()
    {
        $user = new User();
        $user->setCourriel("ancien@example.com");

        $controller = new ModifierProfilCtl();
        $controller->modifierCourriel($user, "nouveau@example.com");

        $this->assertEquals("nouveau@example.com", $user->getCourriel());
    }

    public function testModifierPseudo()
    {
        $user = new User();
        $user->setPseudo("ancienPseudo");

        $controller = new ModifierProfilCtl();
        $controller->modifierPseudo($user, "nouveauPseudo");

        $this->assertEquals("nouveauPseudo", $user->getPseudo());
    }

    public function testModifierBiographie()
    {
        $user = new User();
        $user->setBiographie("Ancienne biographie");

        $controller = new ModifierProfilCtl();
        $controller->modifierBiographie($user, "Nouvelle biographie");

        $this->assertEquals("Nouvelle biographie", $user->getBiographie());
    }

    public function testModifierAvatar()
    {
        $user = new User();
        $user->setAvatar("ancienAvatar.jpg");

        $controller = new ModifierProfilCtl();
        $controller->modifierAvatar($user, "nouvelAvatar.jpg");

        $this->assertEquals("nouvelAvatar.jpg", $user->getAvatar());
    }
}
