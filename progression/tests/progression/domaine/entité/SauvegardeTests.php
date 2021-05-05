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

final class SauvegardeTests extends TestCase
{
	public function test_étant_donné_une_Sauvegarde_instanciée_avec_tous_ses_paramètres_lorsquon_récupère_ses_attributs_on_obtient_des_valeurs_identiques()
	{
		$username_attendu = "jdoe";
        $question_uri_attendu = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";
		$date_sauvegarde_attendue = 1620150294;
		$langage_attendu = "python";
        $code_attendu = "print(\"Hello world!\")";

		$résultat_obtenu = new Sauvegarde
        (
            "jdoe",
            "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
            1620150294,
            "python",
            "print(\"Hello world!\")"
        );

		$this->assertEquals($username_attendu, $résultat_obtenu->username);
		$this->assertEquals($question_uri_attendu, $résultat_obtenu->question_uri);
		$this->assertEquals($date_sauvegarde_attendue, $résultat_obtenu->date_sauvegarde);
        $this->assertEquals($langage_attendu, $résultat_obtenu->langage);
		$this->assertEquals($code_attendu, $résultat_obtenu->code);
	}
}
