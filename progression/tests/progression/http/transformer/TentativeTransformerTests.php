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

use PHPUnit\Framework\TestCase;
use progression\domaine\entité\{TentativeProg, RésultatProg, Commentaire};

final class TentativeTransformerTests extends TestCase
{
	public function test_étant_donné_une_TentativeProg_instanciée_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		$tentative = new TentativeProg(
			"python",
			"codeTest",
			1614711760,
			false,
			2,
			34567,
			"feedBackTest",
			[new RésultatProg("output", "error", false, "feedback", 123)],
			[new Commentaire("Message", "jdoe", 123456, 12)],
		);
		$tentative->id =
			"roger/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711760";
		$tentativeTransformer = new TentativeProgTransformer();
		$résultat = [
			"id" =>
				"roger/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711760",
			"date_soumission" => 1614711760,
			"sous-type" => "tentativeProg",
			"réussi" => false,
			"tests_réussis" => 2,
			"feedback" => "feedBackTest",
			"langage" => "python",
			"code" => "codeTest",
			"temps_exécution" => 34567,
			"links" => [
				"self" =>
					"https://example.com/tentative/roger/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711760",
			],
		];

		$this->assertEquals($résultat, $tentativeTransformer->transform($tentative));
	}

	public function test_étant_donné_une_TentativeProg_instanciée_avec_des_résultats_lorsquon_inclut_les_résultats_on_obtient_un_tableau_de_résultats()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		$tentative = new TentativeProg("python", "codeTest", 1614711760, false, 2, 34567, "feedBackTest", [
			new RésultatProg("output", "error", false, "feedback", 123),
			new RésultatProg("output 2", "error 2", true, "feedback 2", 456),
		]);
		$tentative->id =
			"roger/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711760";
		$tentativeTransformer = new TentativeProgTransformer();

		$inclusionsResultats = $tentativeTransformer->includeResultats($tentative);

		$listeRésultats = [];
		foreach ($inclusionsResultats->getData() as $résultat) {
			$listeRésultats[] = $résultat;
		}

		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/tentativeTransformerTest_1.json",
			json_encode($listeRésultats),
		);
	}

	public function test_étant_donné_une_TentativeProg_instanciée_avec_des_commentaires_lorsquon_inclut_les_commentaires_on_obtient_un_tableau_de_commentaires()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		$tentative = new TentativeProg(
			"python",
			"codeTest",
			1614711760,
			false,
			2,
			34567,
			"feedBackTest",
			[],
			[new Commentaire("Message", "jdoe", 123456, 12), new Commentaire("Message 2", "bob", 654321, 13)],
		);
		$tentative->id =
			"roger/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711760";
		$tentativeTransformer = new TentativeProgTransformer();

		$commentaires = [
			"message" => "Message",
			"créateur" => "jdoe",
			"date" => 123456,
			"numéro_ligne" => 12,
		];

		$inclusionsCommentaires = $tentativeTransformer->includeCommentaires($tentative);

		$listeCommentaires = [];
		foreach ($inclusionsCommentaires->getData() as $commentaire) {
			$listeCommentaires[] = $commentaire;
		}

		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/tentativeTransformerTest_2.json",
			json_encode($listeCommentaires),
		);
	}
}
