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

require_once __DIR__ . "/../../../TestCase.php";

use progression\dao\DAOFactory;
use progression\domaine\entité\{Question, QuestionProg, Avancement, TentativeProg, User};
use progression\http\contrôleur\AvancementCtl;
use Illuminate\Http\Request;
use Illuminate\Auth\GenericUser;

final class AvancementCtlTests extends TestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = new GenericUser(["username" => "jdoe", "rôle" => User::ROLE_NORMAL]);
		$this->admin = new GenericUser(["username" => "admin", "rôle" => User::ROLE_ADMIN]);

		$_ENV["APP_URL"] = "https://example.com/";

		// UserDAO
		$mockUserDAO = Mockery::mock("progression\dao\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("jdoe")
			->andReturn(new User("jdoe"));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("Marcel")
			->andReturn(null);

		// Question
		$question = new QuestionProg();
		$question->type = Question::TYPE_PROG;
		$question->nom = "appeler_une_fonction_paramétrée";
		$question->uri = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";

		$mockQuestionDAO = Mockery::mock("progression\dao\QuestionDAO");
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($question);

		// Avancement
		$avancement = new Avancement([new TentativeProg("python", "codeTest", 1614965817, false, 2, "feedbackTest")]);
		$avancement->etat = 1;
		$avancement->type = Question::TYPE_PROG;
		$avancementPost = new Avancement([], Question::ETAT_DEBUT, Question::TYPE_PROG);

		$mockAvancementDAO = Mockery::mock("progression\dao\AvancementDAO");
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($avancement);
		$mockAvancementDAO
			->shouldReceive("save")
			->with("bob", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", $avancementPost)
			->andReturn($avancementPost);
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("Marcel", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn(null);

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		$mockDAOFactory->shouldReceive("get_question_dao")->andReturn($mockQuestionDAO);
		$mockDAOFactory->shouldReceive("get_avancement_dao")->andReturn($mockAvancementDAO);

		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_le_username_dun_utilisateur_et_le_chemin_dune_question_lorsquon_appelle_get_on_obtient_l_avancement_et_ses_relations_sous_forme_json()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
		);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertStringEqualsFile(
			__DIR__ . "/résultats_attendus/avancementCtlTests_1.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_avancement_inexistant_lorsquon_appelle_get_on_obtient_ressource_non_trouvée()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/avancement/Marcel/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
		);

		$this->assertEquals(404, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Ressource non trouvée."}', $résultat_observé->getContent());
	}

	/*public function test_étant_donné_le_username_dun_utilisateur_et_le_chemin_dune_question_lorsquon_appelle_post_sans_avancement_on_obtient_un_nouvel_avancement_avec_ses_valeurs_par_defaut()
	{
		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/jdoe/avancements", [
			"question_uri" =>
				"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24"
		]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertStringEqualsFile(
			__DIR__ . "/résultats_attendus/avancementCtlTests_1.json",
			$résultat_observé->getContent(),
		);
	}
	public function test_étant_donné_le_username_dun_utilisateur_et_le_chemin_dune_question_lorsquon_appelle_post_avec_un_avancement_on_obtient_une_erreur_403()
	{
		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/jdoe/avancements", [
			"question_uri" =>
				"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			"avancement" => "{test}",
		]);

		$this->assertEquals(403, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Accès interdit."}', $résultat_observé->getContent());
	}
	public function test_étant_donné_le_username_dun_admin_et_le_chemin_dune_question_lorsquon_appelle_post_sans_avancement_dans_le_body_on_obtient_un_message_derreur()
	{
		$résultat_observé = $this->actingAs(new GenericUser(["username" => "admin", "rôle" => User::ROLE_ADMIN]))->call(
			"POST",
			"/user/jdoe/avancements",
			[
				"question_uri" =>
					"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24"
			],
		);
		$this->assertEquals(200, $résultat_observé->status());
		$this->assertStringEqualsFile(
			__DIR__ . "/résultats_attendus/avancementCtlTests_1.json",
			$résultat_observé->getContent(),
		);
	}*/
	public function test_étant_donné_le_username_dun_admin_et_le_chemin_dune_question_lorsquon_appelle_post_avec_avancement_dans_le_body_on_obtient_lavancement_modifié()
	{
		$avancement = new Avancement([], Question::ETAT_REUSSI, Question::TYPE_PROG);
		$résultat_observé = $this->actingAs(new GenericUser(["username" => "admin", "rôle" => User::ROLE_ADMIN]))->call(
			"POST",
			"/user/jdoe/avancements",
			[
				"question_uri" =>
					"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
				"avancement" => "{état:0}",
			],
		);

		$this->assertEquals(200, $résultat_observé->status());		
		$this->assertStringEqualsFile(
			__DIR__ . "/résultats_attendus/avancementCtlTests_1.json",
			$résultat_observé->getContent(),
		);
	}
}
