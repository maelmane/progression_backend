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

use progression\domaine\entité\{QuestionSys, TestSys};
use PHPUnit\Framework\TestCase;

final class QuestionSysTransformerTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$_ENV["APP_URL"] = "https://example.com/";
	}

	public function test_étant_donné_une_questionsys_instanciée_avec_des_valeurs_minimales_lorsquon_le_transforme_on_obtient_un_tableau_d_objets_identique()
	{
		$question = new QuestionSys();
		$question->id = "id";

		$item = (new QuestionSysTransformer())->transform($question);

		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/questionSysTransformerTest_minimal.json",
			json_encode($item),
		);
	}

	public function test_étant_donné_une_questionsys_instanciée_avec_des_valeurs_lorsquon_le_transforme_on_obtient_un_tableau_d_objets_identique()
	{
		$username = "jdoe";

		$question = new QuestionSys();
		$question->id =
			"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24";
		$question->nom = "appeler_une_fonction_paramétrée";
		$question->titre = "Appeler une fonction paramétrée";
		$question->objectif = "Appeler une fonction existante recevant un paramètre";
		$question->description = "Ceci est une question système complète";
		$question->enonce =
			"La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.";
		$question->auteur = "Albert Einstein";
		$question->licence = "poétique";
		$question->niveau = "débutant";
		$question->image = "imageDeLaQuestion";
		$question->utilisateur = "Ginette";
		$question->solution = "laSolution";

		$item = (new QuestionSysTransformer())->transform($question);

		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/questionSysTransformerTest_base.json",
			json_encode($item),
		);
	}

	public function test_étant_donné_une_question_avec_ses_tests_lorsquon_inclut_les_tests_on_reçoit_un_tableau_de_tests_numérotés_dans_le_même_ordre()
	{
		$question = new QuestionSys();
		$question->id =
			"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24";

		$question->tests = [
			new TestSys("Toutes Permissions", "-rwxrwxrwx", "laValidation", "utilisateur"),
			new TestSys("Read Write Permissions", "-rw-rw-rw-", "laValidation2", "utilisateur2", "positif", "négatif"),
		];

		$questionSysTransformer = new QuestionSysTransformer();
		$résultats_obtenus = $questionSysTransformer->includeTests($question);

		$tests = [];
		foreach ($résultats_obtenus->getData() as $résultat) {
			$tests[] = $résultat;
		}

		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/questionSysTransformerTest_inclusion_tests.json",
			json_encode($tests),
		);
	}

	public function test_étant_donné_une_question_sans_tests_lorsquon_inclut_les_tests_on_reçoit_un_tableau_vide()
	{
		$question = new QuestionSys();
		$question->tests = [];
		$question->id =
			"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24";

		$questionSysTransformer = new QuestionSysTransformer();
		$résultat_obtenu = $questionSysTransformer->includeTests($question);

		$this->assertEquals(0, count($résultat_obtenu->getData()));
	}
}
