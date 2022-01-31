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
use progression\domaine\entité\{Question, QuestionProg, Avancement, TentativeProg, User};
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
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("jdoe")
			->andReturn(new User("jdoe"));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("roger")
			->andReturn(new User("roger"));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("Marcel")
			->andReturn(null);

		// Question
		$question = new QuestionProg();
		$question->type = Question::TYPE_PROG;
		$question->nom = "appeler_une_fonction_paramétrée";
		$question->uri = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";

		$mockQuestionDAO = Mockery::mock("progression\\dao\\question\\QuestionDAO");
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($question);

		// Avancement
		$avancement_nouveau = new Avancement();

		$avancement_réussi = new Avancement(Question::ETAT_REUSSI, Question::TYPE_PROG, [
			new TentativeProg("python", "codeTest", [], 1614965817, false, 2, "feedbackTest"),
		]);

		$mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($avancement_réussi);
		$mockAvancementDAO
			->shouldReceive("save")
			->with("jdoe", "https://depot.com/roger/questions_prog/nouvelle_question", Mockery::any())
			->andReturn($avancement_nouveau);

		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/roger/questions_inexistante")
			->andReturn(null);

		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("roger", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($avancement_réussi);
		$mockAvancementDAO
			->shouldReceive("save")
			->with("roger", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", Mockery::any())
			->andReturn($avancement_réussi);

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		$mockDAOFactory->shouldReceive("get_question_dao")->andReturn($mockQuestionDAO);
		$mockDAOFactory->shouldReceive("get_avancement_dao")->andReturn($mockAvancementDAO);

		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	// GET
	public function test_étant_donné_le_username_dun_utilisateur_et_le_chemin_dune_question_lorsquon_appelle_get_on_obtient_l_avancement_et_ses_relations_sous_forme_json()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
		);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/avancementCtlTests_1.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_avancement_inexistant_lorsquon_appelle_get_on_obtient_ressource_non_trouvée()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX2luZXhpc3RhbnRl",
		);

		$this->assertEquals(404, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Ressource non trouvée."}', $résultat_observé->getContent());
	}

	// POST
	public function test_étant_donné_un_utilisateur_inexistant_dans_la_requete_lorsquon_appelle_post_avec_un_avancement_on_obtient_une_erreur_403()
	{
		$avancementTest = ["état" => Question::ETAT_REUSSI];

		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/Marcel/avancements", [
			"question_uri" => "aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvbm91dmVsbGVfcXVlc3Rpb24",
			"avancement" => $avancementTest,
		]);

		$this->assertEquals(403, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Opération interdite."}', $résultat_observé->getContent());
	}

	public function test_étant_donné_le_chemin_dune_question_non_fourni_dans_la_requete_lorsquon_appelle_post_avec_un_avancement_on_obtient_une_erreur_400()
	{
		$avancementTest = ["état" => Question::ETAT_REUSSI];

		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/jdoe/avancements", [
			"avancement" => $avancementTest,
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"question_uri":["Le champ question uri est obligatoire."]}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_le_username_dun_utilisateur_et_le_chemin_dune_question_lorsquon_appelle_post_sans_avancement_on_obtient_un_nouvel_avancement_avec_ses_valeurs_par_defaut()
	{
		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/jdoe/avancements", [
			"question_uri" => "aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvbm91dmVsbGVfcXVlc3Rpb24",
		]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/avancementCtlTests_nouvelAvancement.json",
			$résultat_observé->getContent(),
		);
	}
	public function test_étant_donné_le_username_dun_utilisateur_et_le_chemin_dune_question_lorsquon_appelle_post_avec_un_avancement_on_obtient_une_erreur_403()
	{
		$avancementTest = ["état" => Question::ETAT_REUSSI];

		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/jdoe/avancements", [
			"question_uri" =>
				"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			"avancement" => $avancementTest,
		]);

		$this->assertEquals(403, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Opération interdite."}', $résultat_observé->getContent());
	}
	public function test_étant_donné_un_admin_et_le_chemin_dune_question_lorsquon_appelle_post_sans_avancement_on_obtient_le_meme_resultat_quun_utilisateur_normal()
	{
		$résultat_observé = $this->actingAs($this->admin)->call("POST", "/user/jdoe/avancements", [
			"question_uri" => "aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvbm91dmVsbGVfcXVlc3Rpb24",
		]);
		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/avancementCtlTests_nouvelAvancement.json",
			$résultat_observé->getContent(),
		);
	}
	public function test_étant_donné_un_admin_et_le_chemin_dune_question_lorsquon_appelle_post_avec_avancement_on_obtient_lavancement_modifié()
	{
		$avancementTest = ["état" => Question::ETAT_REUSSI];

		$résultat_observé = $this->actingAs($this->admin)->call("POST", "/user/roger/avancements", [
			"question_uri" =>
				"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			"avancement" => $avancementTest,
		]);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/avancementCtlTests_2.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_admin_et_le_chemin_dune_question_lorsquon_appelle_post_avec_avancement_sans_etat_on_obtient_une_erreur_400()
	{
		$avancementTest = ["test" => "test valeur"];
		$résultat_observé = $this->actingAs($this->admin)->call("POST", "/user/jdoe/avancements", [
			"question_uri" =>
				"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			"avancement" => $avancementTest,
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"avancement.état":["The avancement.état field is required when avancement is present."]}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_admin_et_le_chemin_dune_question_lorsquon_appelle_post_avec_l_état_d_avancement_invalide_on_obtient_une_erreur_400()
	{
		$avancementTest = ["état" => 42];
		$résultat_observé = $this->actingAs($this->admin)->call("POST", "/user/jdoe/avancements", [
			"question_uri" =>
				"aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			"avancement" => $avancementTest,
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals(
			'{"erreur":{"avancement.état":["The avancement.état must be between 0 and 2."]}}',
			$résultat_observé->getContent(),
		);
	}
}
