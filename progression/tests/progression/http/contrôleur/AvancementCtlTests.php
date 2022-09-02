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
use progression\domaine\entité\{Question, QuestionProg, Avancement, TentativeProg, Sauvegarde, User, Commentaire};
use Illuminate\Auth\GenericUser;

final class AvancementCtlTests extends ContrôleurTestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();

		$this->user = new GenericUser(["username" => "jdoe", "rôle" => User::ROLE_NORMAL]);

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

		// Question Appeler une fonction
		$question = new QuestionProg();
		$question->type = Question::TYPE_PROG;
		$question->nom = "appeler_une_fonction_paramétrée";
		$question->uri = "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction";

		$mockQuestionDAO = Mockery::mock("progression\\dao\\question\\QuestionDAO");
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", [])
			->andReturn($question);

		// Question Nouvelle Question
		$question = new QuestionProg();
		$question->type = Question::TYPE_PROG;
		$question->nom = "nouvelle question";
		$question->uri = "https://depot.com/roger/questions_prog/nouvelle_question";
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/roger/questions_prog/nouvelle_question")
			->andReturn($question);

		// Avancement
		$avancement_nouveau = new Avancement();
		$avancement_réussi = new Avancement();
		$avancement_réussi->date_modification = 1614965818;
		$avancement_réussi->date_réussite = 1614965817;
		$avancement_réussi->etat = 2;

		$avancement_réussi_avec_tentatives_et_sauvegardes = new Avancement(
			tentatives: [
				1614965817 => new TentativeProg("python", "codeTest 1", 1614965817, true, [], 2, "feedbackTest"),
				1614965818 => new TentativeProg("python", "codeTest 2", 1614965818, true, [], 2, "feedbackTest"),
			],
			titre: "Titre",
			niveau: "facile",
			sauvegardes: [
				"python" => new Sauvegarde(1614965817, "Test 1"),
				"java" => new Sauvegarde(1614965818, "Test 2"),
			],
		);

		$avancement_réussi_avec_tentatives_commentaires_et_sauvegardes = new Avancement(
			tentatives: [
				1614965817 => new TentativeProg(
					langage: "python",
					code: "codeTest 1",
					date_soumission: 1614965817,
					réussi: true,
					résultats: [],
					tests_réussis: 2,
					feedback: "feedbackTest",
					commentaires: [
						new Commentaire("Ceci est un commentaire", "O. Teur", 1614974921, 42),
						new Commentaire("Ceci est un autre commentaire", "O. Teur", 1614974922, 43),
					],
				),
				1614965818 => new TentativeProg(
					langage: "python",
					code: "codeTest 2",
					date_soumission: 1614965818,
					réussi: true,
					résultats: [],
					tests_réussis: 2,
					feedback: "feedbackTest",
					commentaires: [new Commentaire("Ceci est encore un autre commentaire", "O. Teur", 1614984921, 24)],
				),
			],
			titre: "Titre",
			niveau: "facile",
			sauvegardes: [
				"python" => new Sauvegarde(1614965817, "Test 1"),
				"java" => new Sauvegarde(1614965818, "Test 2"),
			],
		);

		$mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", [])
			->andReturn($avancement_réussi);
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", [
				"tentatives",
				"sauvegardes",
			])
			->andReturn($avancement_réussi_avec_tentatives_et_sauvegardes);
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", [
				"tentatives",
				"tentatives.commentaires",
				"sauvegardes",
			])
			->andReturn($avancement_réussi_avec_tentatives_commentaires_et_sauvegardes);
		$mockAvancementDAO
			->shouldReceive("save")
			->with("jdoe", "https://depot.com/roger/questions_prog/nouvelle_question", Mockery::Any())
			->andReturn($avancement_nouveau);
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/roger/questions_inexistante", [])
			->andReturn(null);

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
	public function test_étant_donné_le_username_dun_utilisateur_et_le_chemin_dune_question_lorsquon_appelle_get_sans_includes_on_obtient_l_avancement_seulement_sous_forme_json()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
		);

		$this->assertResponseStatus(200);
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/avancementCtlTests_avancement_réussi.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_le_username_dun_utilisateur_et_le_chemin_dune_question_lorsquon_appelle_get_avec_includes_on_obtient_l_avancement_et_ses_tentatives_et_sauvegardes_sous_forme_json()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24?include=tentatives,sauvegardes",
		);

		$this->assertResponseStatus(200);
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/avancementCtlTests_avancement_réussi_avec_tentatives_et_sauvegardes.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_le_username_dun_utilisateur_et_le_chemin_dune_question_lorsquon_appelle_get_en_incluant_les_tentatives_avec_commentaires_et_sauvegardes_on_obtient_l_avancement_et_ses_sauvegardes_et_tentatives_avec_ses_commentaires_sous_forme_json()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24?include=tentatives.commentaires,sauvegardes",
		);

		$this->assertResponseStatus(200);
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ .
				"/résultats_attendus/avancementCtlTests_avancement_réussi_avec_tentatives_commentaires_et_sauvegardes.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_avancement_inexistant_lorsquon_appelle_get_on_obtient_ressource_non_trouvée()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX2luZXhpc3RhbnRl",
		);

		$this->assertResponseStatus(404);
		$this->assertEquals('{"erreur":"Ressource non trouvée."}', $résultat_observé->getContent());
	}

	public function test_étant_donné_le_chemin_dune_question_non_fourni_dans_la_requete_lorsquon_appelle_post_avec_un_avancement_on_obtient_une_erreur_400()
	{
		$avancementTest = ["état" => Question::ETAT_REUSSI];

		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/jdoe/avancements", [
			"avancement" => $avancementTest,
		]);

		$this->assertResponseStatus(400);
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

		$this->assertResponseStatus(200);
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/avancementCtlTests_nouvelAvancement.json",
			$résultat_observé->getContent(),
		);
	}
}
