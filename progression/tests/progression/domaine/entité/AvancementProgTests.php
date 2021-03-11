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

final class AvancementProgTests extends TestCase
{
	public function test_étant_donné_un_AvancementProg_instancié_avec_tous_ses_paramètres_lorsquon_récupère_ses_attributs_on_obtient_des_valeurs_identiques()
	{
		$question_uri_attendu = "http://exemple.com/maquestion";
		$username_attendu = "jdoe";
		$réponses_attendu = ["exemple_réponse"];

		$résultat_obtenu = new AvancementProg("http://exemple.com/maquestion", "jdoe", ["exemple_réponse"]);

		$this->assertEquals($question_uri_attendu, $résultat_obtenu->question_uri);
		$this->assertEquals($username_attendu, $résultat_obtenu->username);
		$this->assertEquals($réponses_attendu, $résultat_obtenu->réponses);
	}
	public function test_étant_donné_un_AvancementProg_instancié_sans_réponses_lorsquon_récupère_ses_réponses_on_obtient_un_tableau_vide()
	{
		$résultat_attendu = [];
		$résultat_obtenu =
			(new AvancementProg("http://exemple.com/maquestion", "jdoe"))
			->réponses;

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}
}
