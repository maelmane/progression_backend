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

use progression\TestCase;

use progression\dao\DAOFactory;
use progression\dao\exécuteur\ExécutionException;
use progression\domaine\entité\{Avancement, Test, Exécutable, Question, TentativeProg, QuestionProg, User};

use Illuminate\Auth\GenericUser;

final class TentativeCtlTests extends TestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();

		$_ENV["AUTH_TYPE"] = "no";
		$_ENV["APP_URL"] = "https://example.com/";

		$this->user = new GenericUser(["username" => "jdoe", "rôle" => User::ROLE_NORMAL]);

		// Tentative
		$tentative = new TentativeProg("python", "codeTest", "1614374490");
		$tentative->tests_réussis = 2;
		$tentative->réussi = true;
		$tentative->feedback = "feedbackTest";

		$mockTentativeDAO = Mockery::mock("progression\\dao\\tentative\\TentativeDAO");
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "prog1/les_fonctions_01/appeler_une_fonction_paramétrée", "9999999999")
			->andReturn(null);
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "prog1/les_fonctions_01/appeler_une_fonction_paramétrée", "1614374490")
			->andReturn($tentative);
		$mockTentativeDAO->shouldReceive("save")->andReturn($tentative);

		// Question
		$question = new QuestionProg();
		$question->type = Question::TYPE_PROG;
		$question->nom = "appeler_une_fonction_paramétrée";
		$question->uri = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";
		$question->feedback_pos = "Bon travail!";
		$question->feedback_neg = "Encore un effort!";
		$question->feedback_err = "oups!";
		// Ébauches
		$question->exécutables["python"] = new Exécutable("#+TODO\nprint(\"Hello world!\")", "python");
		$question->exécutables["java"] = new Exécutable("//+TODO\nSystem.out.println(\"Hello world!\")", "java");
		// Tests
		$question->tests = [
			new Test("2 salutations", "Bonjour\nBonjour\n", "2", "", "C'est ça!", "C'est pas ça :(", "arrrg!"),
		];

		$mockQuestionDAO = Mockery::mock("progression\\dao\\question\\QuestionDAO");
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($question);

		// Exécuteur
		$mockExécuteur = Mockery::mock("progression\\dao\\exécuteur\\Exécuteur");
		$mockExécuteur
			->shouldReceive("exécuter")
			->withArgs(function ($exec, $test) {
				return $exec->lang == "python";
			})
			->andReturn([["output" => "Bonjour\nAllo\n", "errors" => ""]]);
		$mockExécuteur
			->shouldReceive("exécuter")
			->withArgs(function ($exec, $test) {
				return $exec->lang == "java";
			})
			->andThrow(new ExécutionException(null, "test://TentativeCtlTests.php"));

		// Avancement
		$avancement = new Avancement(Question::ETAT_REUSSI, Question::TYPE_PROG, [
			new TentativeProg("python", "codeTest", 1614965817, false, 2, "feedbackTest"),
		]);

		$mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($avancement);

		$mockAvancementDAO->shouldReceive("save")->andReturn($avancement);

		// User
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->allows("get_user")
			->with("jdoe")
			->andReturn(new User("jdoe"));

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_tentative_dao")->andReturn($mockTentativeDAO);
		$mockDAOFactory->shouldReceive("get_tentative_prog_dao")->andReturn($mockTentativeDAO);
		$mockDAOFactory->shouldReceive("get_question_dao")->andReturn($mockQuestionDAO);
		$mockDAOFactory->shouldReceive("get_exécuteur")->andReturn($mockExécuteur);
		$mockDAOFactory->shouldReceive("get_avancement_dao")->andReturn($mockAvancementDAO);
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);

		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
		DAOFactory::setInstance(null);
	}

	public function test_étant_donné_le_username_dun_utilisateur_le_chemin_dune_question_et_le_timestamp_lorsquon_appelle_get_on_obtient_la_TentativeProg_et_ses_relations_sous_forme_json()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"GET",
			"/tentative/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490",
		);

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/tentativeCtlTest_2.json",
			$résultat_obtenu->getContent(),
		);
	}

	public function test_étant_donné_le_username_dun_utilisateur_le_chemin_dune_question_et_un_timestamp_inexistant_lorsquon_appelle_get_on_obtient_ressource_non_trouvée()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"GET",
			"/tentative/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/9999999999",
		);

		$this->assertEquals(404, $résultat_obtenu->status());
		$this->assertEquals('{"erreur":"Ressource non trouvée."}', $résultat_obtenu->getContent());
	}

	public function test_étant_donné_le_username_dun_utilisateur_le_chemin_dune_question_et_le_timestamp_lorsquon_appelle_post_on_obtient_la_TentativeProg_avec_ses_résultats_et_ses_relations_sous_forme_json()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/tentatives?include=resultats",
			["langage" => "python", "code" => "#+TODO\nprint(\"Hello world!\")"],
		);
		$this->assertEquals(200, $résultat_obtenu->status());
		$heure_courante = time();
		$heure_tentative = json_decode($résultat_obtenu->getContent())->data->attributes->date_soumission;
		if ($heure_courante - $heure_tentative > 1) {
			$this->fail();
		}

		$this->assertJsonStringEqualsJsonString(
			sprintf(file_get_contents(__DIR__ . "/résultats_attendus/tentativeCtlTest_1.json"), $heure_tentative),
			$résultat_obtenu->getContent(),
		);
	}

	public function test_étant_donné_une_soumission_sans_code_lorsquon_appelle_post_on_obtient_une_erreur_de_validation()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/tentatives",
			["langage" => "python"],
		);

		$this->assertEquals(400, $résultat_obtenu->status());
		$this->assertEquals('{"erreur":{"code":["Le champ code est obligatoire."]}}', $résultat_obtenu->getContent());
	}

	public function test_étant_donné_un_url_de_compilebox_inaccessible_lorsquon_appelle_post_on_obtient_Service_non_disponible()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/tentatives",
			["langage" => "java", "code" => "#+TODO\nprint(\"on ne se rendra pas à exécuter ceci\")"],
		);

		$this->assertEquals(503, $résultat_obtenu->status());
		$this->assertEquals('{"erreur":"Service non disponible."}', $résultat_obtenu->getContent());
	}

	public function test_étant_donné_une_tentative_invalide_lorsquon_appelle_post_on_obtient_Tentative_intraitable()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/tentatives",
			["langage" => "python", "code" => "print(\"Hello world!\")"],
		);

		$this->assertEquals(400, $résultat_obtenu->status());
		$this->assertEquals('{"erreur":"Requête intraitable."}', $résultat_obtenu->getContent());
	}
}
