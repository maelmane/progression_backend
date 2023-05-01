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

namespace progression\domaine\entité\user;

use PHPUnit\Framework\TestCase;
use \InvalidArgumentException;

final class UserTests extends TestCase
{
	public function test_étant_donné_un_User_instancié_avec_tous_ses_paramètres_lorsquon_récupère_ses_attributs_on_obtient_des_valeurs_identiques()
	{
		$username_attendu = "bob";
		$courriel_attendu = "bob@gmail.com";
		$résultat_obtenu = new User("bob", "bob@gmail.com", état: État::ACTIF, rôle: Rôle::NORMAL);

		$état_attendu = État::ACTIF;
		$rôle_attendu = Rôle::NORMAL;

		$this->assertEquals($username_attendu, $résultat_obtenu->username);
		$this->assertEquals($courriel_attendu, $résultat_obtenu->courriel);
		$this->assertEquals($état_attendu, $résultat_obtenu->état);
		$this->assertEquals($rôle_attendu, $résultat_obtenu->rôle);
	}
}
