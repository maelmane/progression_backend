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

final class RéponseProgTests extends TestCase
{
	public function test_étant_donné_une_RéponseProg_instanciée_avec_tous_ses_paramètres_lorsquon_récupère_ses_attributs_on_obtient_des_valeurs_identiques()
	{
		$langage_attendu = "python";
		$code_attendu = "print('Hello, world!')";
		$date_soumission_attendu = 1111;
		$tests_réussis_attendu = "exemple_test_réussis";
		$feedback_attendu = "exemple_feedback";

		$résultat_obtenu  = new RéponseProg(
			"python",
			"print('Hello, world!')",
			1111,
			"exemple_test_réussis",
			"exemple_feedback"
		);

		$this->assertEquals($langage_attendu, $résultat_obtenu->langage);
		$this->assertEquals($code_attendu, $résultat_obtenu->code);
		$this->assertEquals($date_soumission_attendu, $résultat_obtenu->date_soumission);
		$this->assertEquals($tests_réussis_attendu, $résultat_obtenu->tests_réussis);
		$this->assertEquals($feedback_attendu, $résultat_obtenu->feedback);
	}

	public function test_étant_donné_une_RéponseProg_instanciée_avec_ses_paramètres_null_lorsquon_récupère_ses_attributs_on_obtient_des_valeurs_nulles()
	{
		$date_soumission_attendu = null;
		$tests_réussis_attendu = null;
		$feedback_attendu = null;

		$résultat_obtenu  = new RéponseProg("python", "print('Hello, world!')");

		$this->assertEquals($date_soumission_attendu, $résultat_obtenu->date_soumission);
		$this->assertEquals($tests_réussis_attendu, $résultat_obtenu->tests_réussis);
		$this->assertEquals($feedback_attendu, $résultat_obtenu->feedback);
	}
}
