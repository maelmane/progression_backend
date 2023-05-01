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

use progression\ContrôleurTestCase;

use progression\dao\DAOFactory;
use progression\domaine\entité\{QuestionProg, Question, TestProg, QuestionSys, TestSys};
use progression\domaine\entité\user\{User, Rôle};

use Illuminate\Auth\GenericUser;

final class TestCtlTests extends ContrôleurTestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = new GenericUser(["username" => "bob", "rôle" => Rôle::NORMAL]);

		$_ENV["APP_URL"] = "https://example.com/";

		// Question
		$question = new QuestionProg();
		$question->type = Question::TYPE_PROG;
		$question->nom = "appeler_une_fonction_paramétrée";
		$question->uri = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";
		$question->tests = [
			new TestProg("2 salutations", "Bonjour\nBonjour\n", "2"),
			new TestProg("Aucune salutation", "", "0"),
		];

		$mockQuestionDAO = Mockery::mock("progression\\dao\\question\\QuestionDAO");
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($question);

		$question = new QuestionSys();
		$question->type = Question::TYPE_SYS;
		$question->nom = "Persmissions 2";
		$question->uri = "https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions2";
		$question->tests = [
			new TestSys("testNom", "testSortie", "testValidation", "utilisateurTest", "testFbp", "testFbn"),
			new TestSys("testNom2", "testSortie2", "testValidation2", "utilisateurTest2", "testFbp2", "testFbn2"),
		];

		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions2")
			->andReturn($question);
		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_question_dao")->andReturn($mockQuestionDAO);

		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_le_chemin_dune_questionProg_et_son_test_numero_0_lorsquon_appelle_get_on_obtient_le_test_numero_0_et_ses_relations_sous_forme_json()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"GET",
			"/test/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/0",
		);

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/testCtlTest_question_prog.json",
			$résultat_obtenu->getContent(),
		);
	}

	public function test_étant_donné_le_chemin_dune_questionSys_et_son_test_numero_0_lorsquon_appelle_get_on_obtient_le_test_numero_0_et_ses_relations_sous_forme_json()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"GET",
			"/test/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3N5cy9wZXJtaXNzaW9uczAxL29jdHJveWVyX3RvdXRlc19sZXNfcGVybWlzc2lvbnMy/0",
		);

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/testCtlTest_question_sys.json",
			$résultat_obtenu->getContent(),
		);
	}

	public function test_étant_donné_le_chemin_dune_question_et_son_test_numero_abc_lorsquon_appelle_get_on_obtient_ressource_non_trouvée()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"GET",
			"/test/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/999",
		);

		$this->assertEquals(404, $résultat_obtenu->status());
		$this->assertEquals('{"erreur":"Ressource non trouvée."}', $résultat_obtenu->getContent());
	}
}
