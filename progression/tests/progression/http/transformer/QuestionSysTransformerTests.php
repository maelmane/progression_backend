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

	public function test_étant_donné_une_questionsys_instanciée_avec_des_valeurs_lorsquon_le_transforme_on_obtient_un_tableau_d_objets_identique()
	{
		$username = "jdoe";

		$question = new QuestionSys();
		$question->nom = "appeler_une_fonction_paramétrée";
		$question->uri = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";
		$question->titre = "Appeler une fonction paramétrée";
		$question->description = "Appel d\'une fonction existante recevant un paramètre";
		$question->enonce =
			"La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.";
		$question->auteur = "Albert Einstein";
		$question->licence = "poétique";
		$question->niveau = "débutant";
		$question->image = "imageDeLaQuestion";
		$question->utilisateur = "Ginette";
		$question->solution = "laSolution";

		$item = (new QuestionSysTransformer())->transform([
			"question" => $question,
			"username" => $username,
		]);

		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/questionSysTransformerTest_1.json",
			json_encode($item),
		);
	}

	public function test_étant_donné_une_question_avec_ses_tests_lorsquon_inclut_les_tests_on_reçoit_un_tableau_de_tests_numérotés_dans_le_même_ordre()
	{
		$question = new QuestionSys();
		$question->uri = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";

		$question->tests = [
			new TestSys("Toutes Permissions", "-rwxrwxrwx", "laValidation", "utilisateur"),
			new TestSys("Read Write Permissions", "-rw-rw-rw-", "laValidation2", "utilisateur2", "positif", "négatif"),
		];

		$questionSysTransformer = new QuestionSysTransformer();

		$résultats_obtenus = $questionSysTransformer->includeTests([
			"question" => $question,
			"username" => "Bob",
		]);

		$tests = [];
		foreach ($résultats_obtenus->getData() as $résultat) {
			$tests[] = $résultat;
		}

		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/questionSysTransformerTest_2.json",
			json_encode($tests),
		);
	}

	public function test_étant_donné_une_question_sans_tests_lorsquon_inclut_les_tests_on_reçoit_un_tableau_vide()
	{
		$question = new QuestionSys();

		$question->tests = [];

		$questionSysTransformer = new QuestionSysTransformer();
		$résultat_obtenu = $questionSysTransformer->includeTests([
			"question" => $question,
			"username" => "Bob",
		]);

		$this->assertEquals(0, count($résultat_obtenu->getData()));
	}
}
