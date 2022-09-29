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
use progression\domaine\entité\{TentativeSys, Résultat, Commentaire};

final class TentativeSysTransformerTests extends TestCase
{
	public function test_étant_donné_une_TentativeSys_instanciée_avec_des_valeurs_minimales_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		$tentative = new TentativeSys("conteneurDeLaTentative", "oui");
		$tentative->id = "id";

		$tentativeTransformer = new TentativeSysTransformer("roger/uri");
		$résultat = [
			"id" => "roger/uri/id",
			"date_soumission" => null,
			"sous-type" => "tentativeSys",
			"réussi" => false,
			"tests_réussis" => 0,
			"feedback" => "",
			"conteneur" => "conteneurDeLaTentative",
			"réponse" => "oui",
			"temps_exécution" => null,
			"links" => [
				"self" => "https://example.com/tentative/roger/uri/id",
			],
		];

		$this->assertEquals($résultat, $tentativeTransformer->transform($tentative));
	}

	public function test_étant_donné_une_TentativeSys_instanciée_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		$tentative = new TentativeSys(
			"conteneurDeLaTentative",
			"oui",
			1614711760,
			false,
			[new Résultat("output", "", false, "feedback", 123)],
			2,
			34567,
			"feedBackTest",
			[new Commentaire("Message", "jdoe", 123456, 12)],
		);
		$tentative->id = "1614711760";

		$tentativeTransformer = new TentativeSysTransformer(
			"roger/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
		);
		$résultat = [
			"id" =>
				"roger/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711760",
			"date_soumission" => 1614711760,
			"sous-type" => "tentativeSys",
			"réussi" => false,
			"tests_réussis" => 2,
			"feedback" => "feedBackTest",
			"conteneur" => "conteneurDeLaTentative",
			"réponse" => "oui",
			"temps_exécution" => 34567,
			"links" => [
				"self" =>
					"https://example.com/tentative/roger/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/1614711760",
			],
		];

		$this->assertEquals($résultat, $tentativeTransformer->transform($tentative));
	}

	public function test_étant_donné_une_TentativeSys_instanciée_avec_des_résultats_lorsquon_inclut_les_résultats_on_obtient_un_tableau_de_résultats()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		$tentative = new TentativeSys(
			"conteneurDeLaTentative",
			"oui",
			1614711760,
			false,
			[new Résultat("output", "", false, "feedback", 123), new Résultat("output 2", "", true, "feedback 2", 456)],
			2,
			34567,
			"feedBackTest",
			[new Commentaire("Message", "jdoe", 123456, 12)],
		);
		$tentative->id = "1614711760";

		$tentativeTransformer = new TentativeSysTransformer(
			"roger/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
		);

		$inclusionsResultats = $tentativeTransformer->includeResultats($tentative);

		$listeRésultats = [];
		foreach ($inclusionsResultats->getData() as $résultat) {
			$listeRésultats[] = $résultat;
		}

		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/tentativeSysTransformerTest_inclusion_résultats.json",
			json_encode($listeRésultats),
		);
	}

	public function test_étant_donné_une_TentativeSys_instanciée_avec_des_commentaires_lorsquon_inclut_les_commentaires_on_obtient_un_tableau_de_commentaires()
	{
		$_ENV["APP_URL"] = "https://example.com/";

		$tentative = new TentativeSys(
			"conteneurDeLaTentative",
			"oui",
			1614711760,
			false,
			2,
			34567,
			"feedBackTest",
			[],
			[new Commentaire("Message", "jdoe", 123456, 12), new Commentaire("Message 2", "bob", 654321, 13)],
		);
		$tentative->id = "1614711760";

		$tentativeTransformer = new TentativeSysTransformer(
			"roger/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
		);

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
			__DIR__ . "/résultats_attendus/tentativeSysTransformerTest_inclusion_commentaires.json",
			json_encode($listeCommentaires),
		);
	}
}
