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

namespace progression\domaine\entité;

use PHPUnit\Framework\TestCase;
use \InvalidArgumentException;

final class UserTests extends TestCase
{
	public function test_étant_donné_un_User_instancié_avec_tous_ses_paramètres_lorsquon_récupère_ses_attributs_on_obtient_des_valeurs_identiques()
	{
		$username_attendu = "bob";
		$courriel_attendu = "bob@gmail.com";
		$état_attendu = User::ÉTAT_ACTIF;
		$rôle_attendu = 0;

		$résultat_obtenu = new User("bob", "bob@gmail.com", User::ÉTAT_ACTIF);

		$this->assertEquals($username_attendu, $résultat_obtenu->username);
		$this->assertEquals($courriel_attendu, $résultat_obtenu->courriel);
		$this->assertEquals($état_attendu, $résultat_obtenu->état);
		$this->assertEquals($rôle_attendu, $résultat_obtenu->rôle);
	}

	public function test_étant_donné_un_nouvel_User_lorsquon_linstancie_avec_un_état_invalide_on_obtient_une_InvalidArgumentException()
	{
		$this->expectException(InvalidArgumentException::class);

		$résultat_obtenu = new User("bob", "bob@gmail.com", état: -1);
	}

	public function test_étant_donné_un_User_actif_lorsquon_lui_donne_un_état_invalide_on_obtient_une_InvalidArgumentException()
	{
		$this->expectException(InvalidArgumentException::class);

		$résultat_obtenu = new User("bob");
		$résultat_obtenu->état = -1;
	}
}
