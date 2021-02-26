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

namespace progression\http\transformer;

use progression\domaine\entité\{AvancementProg, RéponseProg};
use PHPUnit\Framework\TestCase;

final class AvancementProgTransformerTests extends TestCase
{
	public function test_étant_donné_un_avancement_instancié_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_array_d_objets_identique()
	{
		$user_id = "jdoe";
		$question_id = "prog1/les_fonctions_01/appeler_une_fonction_paramétrée";
		$_ENV["APP_URL"] = "https://example.com/";

		$avancementProgTransformer = new AvancementProgTransformer();
		$avancement = new AvancementProg("prog1/les_fonctions_01/appeler_une_fonction_paramétrée", "jdoe");

		$résultat = [
			"id" => "jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=",
			"user_id" => "jdoe",
			"état" => 0,
			"links" => [
				"self" =>
					"https://example.com/avancement/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=",
			],
		];

		$this->assertEquals($résultat, $avancementProgTransformer->transform($avancement));
	}

	public function test_étant_donné_un_avancement_avec_ses_tentatives_lorsquon_inclut_les_tentatives_on_reçoit_un_tableau_de_tentatives()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		$tentativeTransformer = new TentativeTransformer();
		$tentative = new RéponseProg(10, "codeTest");
		$tentative->date_soumission = "dateTest";
		$tentative->tests_réussis = 2;
		$tentative->feedback = "feedbackTest";

		$résultat = [
			"id" => "dateTest",
			"date_soumission" => "dateTest",
			"tests_réussis" => 2,
			"feedback" => "feedbackTest",
			"langage" => 10,
			"code" => "codeTest",
		];

		$this->assertEquals($résultat, $tentativeTransformer->transform($tentative));
	}
}
