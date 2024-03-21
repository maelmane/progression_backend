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

use Carbon\Carbon;
use progression\TestCase;
use \InvalidArgumentException;

final class UserTests extends TestCase
{
	public function test_étant_donné_un_User_instancié_avec_tous_ses_paramètres_lorsquon_récupère_ses_attributs_on_obtient_des_valeurs_identiques()
	{
		$username_attendu = "bob";
		$courriel_attendu = "bob@gmail.com";
		$état_attendu = État::ACTIF;
		$rôle_attendu = Rôle::NORMAL;
		$date_inscription_attendu = 1615420800;
		$prénom_attendu = "Bob";
		$nom_attendu = "Paul";
		$nom_complet_attendu = "Bob Paul";
		$pseudo_attendu = "bobby";
		$biographie_attendu = "biographie test";
		$occupation_attendue = Occupation::AUTRE;
		$avatar_attendu = "https://example.com/image";

		Carbon::setTestNow(Carbon::create(2021, 3, 11, 0, 0, 0));
		$résultat_obtenu = new User(
			username: "bob",
			date_inscription: 1615420800,
			courriel: "bob@gmail.com",
			état: État::ACTIF,
			rôle: Rôle::NORMAL,
			prénom: "Bob",
			nom: "Paul",
			nom_complet: "Bob Paul",
			pseudo: "bobby",
			biographie: "biographie test",
			occupation: Occupation::AUTRE,
			avatar: "https://example.com/image",

		);
		Carbon::setTestNow();

		$this->assertEquals($username_attendu, $résultat_obtenu->username);
		$this->assertEquals($courriel_attendu, $résultat_obtenu->courriel);
		$this->assertEquals($état_attendu, $résultat_obtenu->état);
		$this->assertEquals($rôle_attendu, $résultat_obtenu->rôle);
		$this->assertEquals($date_inscription_attendu, $résultat_obtenu->date_inscription);
		$this->assertEquals($prénom_attendu, $résultat_obtenu->prenom);
		$this->assertEquals($nom_attendu, $résultat_obtenu->nom);
		$this->assertEquals($nom_complet_attendu, $résultat_obtenu->nom_complet);
		$this->assertEquals($pseudo_attendu, $résultat_obtenu->pseudo);
		$this->assertEquals($biographie_attendu, $résultat_obtenu->biographie);
		$this->assertEquals($occupation_attendue, $résultat_obtenu->occupation);
		$this->assertEquals($avatar_attendu, $résultat_obtenu->avatar);
	}
}
