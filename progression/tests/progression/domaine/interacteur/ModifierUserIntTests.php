<?php
/*
   This file is part of Progression.

   Progression is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Progression is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Progression.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace progression\domaine\interacteur;

use progression\domaine\entité\{User};
use PHPUnit\Framework\TestCase;

final class ModifierUserIntTests extends TestCase
{
	public function test_étant_donné_un_utilisateur_sans_préférences_lorsquon_lui_ajoute_des_préférences_on_obtient_le_même_utilisateur_avec_des_préférences()
	{
		$user_test = new User("bob");

		$interacteur = new ModifierUserInt();
		$user_modifié = $interacteur->modifier_préférences($user_test, "mes préférences");

		$this->assertEquals(new User("bob", préférences: "mes préférences"), $user_test);
		$this->assertEquals(new User("bob", préférences: "mes préférences"), $user_modifié);
	}

	public function test_étant_donné_un_utilisateur_avec_préférences_lorsquon_modifie_des_préférences_on_obtient_le_même_utilisateur_avec_des_nouvelles_préférences()
	{
		$user_test = new User("bob", préférences: "des préférences originales");

		$interacteur = new ModifierUserInt();
		$user_modifié = $interacteur->modifier_préférences($user_test, "d'autres préférences");

		$this->assertEquals(new User("bob", préférences: "d'autres préférences"), $user_test);
		$this->assertEquals(new User("bob", préférences: "d'autres préférences"), $user_modifié);
	}

	public function test_étant_donné_un_utilisateur_inactif_lorsquon_modifie_son_état_pour_actif_on_obtient_un_utilisateur_actif()
	{
		$user_test = new User("bob", état: ÉTAT::INACTIF);

		$interacteur = new ModifierUserInt();
		$user_modifié = $interacteur->modifier_état($user_test, ÉTAT::ACTIF);

		$this->assertEquals(new User("bob", état: ÉTAT::ACTIF), $user_test);
		$this->assertEquals(new User("bob", état: ÉTAT::ACTIF), $user_modifié);
	}

	public function test_étant_donné_un_utilisateur_en_attente_lorsquon_modifie_son_état_pour_actif_on_obtient_un_utilisateur_actif()
	{
		$user_test = new User("bob", état: ÉTAT::ATTENTE_DE_VALIDATION);

		$interacteur = new ModifierUserInt();
		$user_modifié = $interacteur->modifier_état($user_test, ÉTAT::ACTIF);

		$this->assertEquals(new User("bob", état: ÉTAT::ACTIF), $user_test);
		$this->assertEquals(new User("bob", état: ÉTAT::ACTIF), $user_modifié);
	}

	public function test_étant_donné_un_utilisateur_en_attente_lorsquon_modifie_son_état_pour_inactif_on_obtient_un_utilisateur_inactif()
	{
		$user_test = new User("bob", état: ÉTAT::ATTENTE_DE_VALIDATION);

		$interacteur = new ModifierUserInt();
		$user_modifié = $interacteur->modifier_état($user_test, ÉTAT::INACTIF);

		$this->assertEquals(new User("bob", état: ÉTAT::INACTIF), $user_test);
		$this->assertEquals(new User("bob", état: ÉTAT::INACTIF), $user_modifié);
	}

	public function test_étant_donné_un_utilisateur_inactif_lorsquon_modifie_son_état_pour_en_attente_on_obtient_une_exception()
	{
		$user_test = new User("bob", état: ÉTAT::INACTIF);

		$interacteur = new ModifierUserInt();

		$this->expectException(\DomainException::class);

		$user_modifié = $interacteur->modifier_état($user_test, ÉTAT::ATTENTE_DE_VALIDATION);
	}
}
