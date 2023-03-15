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
use progression\dao\exécuteur\ExécutionException;
use progression\domaine\entité\{Avancement, TestSys, Exécutable, Question, QuestionSys, Résultat, TentativeSys, User};

use Illuminate\Auth\GenericUser;

final class TentativeCtl_QuestionSys_Tests extends ContrôleurTestCase
{
	public $user;
	public $avancement_réussi;
	public $avancement_non_réussi;
	public $tentative_réussie;
	public $tentative_non_réussie;

	public function setUp(): void
	{
		parent::setUp();

		$_ENV["AUTH_TYPE"] = "no";
		$_ENV["APP_URL"] = "https://example.com/";

		$this->user = new GenericUser(["username" => "jdoe", "rôle" => User::ROLE_NORMAL]);

		// QuestionSys avec solution courte
		$question_solution_courte_réussie = new QuestionSys(
			niveau: "Débutant",
			titre: "Question à solution courte",
			utilisateur: "utilisateurTest",
			image: "imageTest",
			solution: "Bonne réponse",
			feedback_pos: "Bon travail!",
			feedback_neg: "Encore un effort!",
		);

		$question_solution_courte_non_réussie = $question_solution_courte_réussie;

		//QuestionSys avec validations
		$question_validée_réussie = new QuestionSys(
			niveau: "Débutant",
			titre: "Question validée",
			utilisateur: "utilisateurTest",
			image: "imageTest",
			feedback_pos: "Bon travail!",
			feedback_neg: "Encore un effort!",
			tests: [
				new TestSys(
					nom: "Toutes permissions 3",
					sortie_attendue: "-rwxrwxrwx",
					validation: "laValidation",
					utilisateur: "momo",
					feedback_pos: "yes!",
					feedback_neg: "non!",
				),
			],
		);

		$question_validée_non_réussie = $question_validée_réussie;

		$mockQuestionDAO = Mockery::mock("progression\\dao\\question\\QuestionDAO");
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/question_solution_courte_réussie")
			->andReturn($question_solution_courte_réussie);
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/question_validée_réussie")
			->andReturn($question_validée_réussie);
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/question_solution_courte_non_réussie")
			->andReturn($question_solution_courte_non_réussie);
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/question_validée_non_réussie")
			->andReturn($question_validée_non_réussie);
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/nouvelle_question")
			->andReturn($question_solution_courte_réussie);

		// Tentatives
		$this->tentative_solution_courte_non_réussie = new TentativeSys(
			conteneur: ["id" => "Conteneur de test incorrect", "ip" => null, "port" => null],
			réponse: "Mauvaise réponse",
			date_soumission: 1614374490,
			réussi: false,
			feedback: "Encore un effort!",
			tests_réussis: 0,
			temps_exécution: 0,
			résultats: [],
		);
		$this->tentative_solution_courte_réussie = new TentativeSys(
			conteneur: ["id" => "Conteneur de test correct", "ip" => null, "port" => null],
			réponse: "Bonne réponse",
			date_soumission: 1614374491,
			réussi: true,
			feedback: "Bon travail!",
			tests_réussis: 1,
			temps_exécution: 0,
			résultats: [],
		);
		$this->tentative_validée_non_réussie = new TentativeSys(
			conteneur: ["id" => "Conteneur de test incorrect", "ip" => null, "port" => null],
			réponse: null,
			date_soumission: 1614374490,
			réussi: false,
			feedback: "Encore un effort!",
			tests_réussis: 0,
			temps_exécution: 0,
			résultats: [new Résultat(sortie_observée: "valide", sortie_erreur: "", résultat: true, temps_exécution: 0)],
		);
		$this->tentative_validée_réussie = new TentativeSys(
			conteneur: ["id" => "Conteneur de test correct", "ip" => null, "port" => null],
			réponse: null,
			date_soumission: 1614374491,
			réussi: true,
			feedback: "Bon travail!",
			tests_réussis: 1,
			temps_exécution: 0,
			résultats: [
				new Résultat(sortie_observée: "invalide", sortie_erreur: "", résultat: false, temps_exécution: 0),
			],
		);

		$mockTentativeDAO = Mockery::mock("progression\\dao\\tentative\\TentativeDAO");
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "https://depot.com/question_solution_courte_non_réussie", 1614374490, Mockery::Any())
			->andReturn($this->tentative_solution_courte_non_réussie);
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "https://depot.com/question_solution_courte_reussie", 1614374491, Mockery::Any())
			->andReturn($this->tentative_solution_courte_réussie);
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "https://depot.com/question_validée_non_réussie", 1614374490, Mockery::Any())
			->andReturn($this->tentative_validée_non_réussie);
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "https://depot.com/question_validée_réussie", 1614374491, Mockery::Any())
			->andReturn($this->tentative_validée_réussie);

		$mockTentativeDAO
			->shouldReceive("get_toutes")
			->with("jdoe", "https://depot.com/question_validée_réussie", 1614374491, Mockery::Any())
			->andReturn([$this->tentative_validée_non_réussie, $this->tentative_validée_réussie]);

		$mockTentativeDAO
			->shouldReceive("get_dernière")
			->with("jdoe", "https://depot.com/question_solution_courte_non_réussie", Mockery::Any())
			->andReturn($this->tentative_solution_courte_non_réussie);
		$mockTentativeDAO
			->shouldReceive("get_dernière")
			->with("jdoe", "https://depot.com/question_solution_courte_réussie", Mockery::Any())
			->andReturn($this->tentative_solution_courte_réussie);
		$mockTentativeDAO
			->shouldReceive("get_dernière")
			->with("jdoe", "https://depot.com/question_validée_non_réussie", Mockery::Any())
			->andReturn($this->tentative_validée_non_réussie);
		$mockTentativeDAO
			->shouldReceive("get_dernière")
			->with("jdoe", "https://depot.com/question_validée_réussie", Mockery::Any())
			->andReturn($this->tentative_validée_réussie);
		$mockTentativeDAO
			->shouldReceive("get_dernière")
			->with("jdoe", "https://depot.com/nouvelle_question", Mockery::Any())
			->andReturn(null);

		// Exécuteur
		$mockExécuteur = Mockery::mock("progression\\dao\\exécuteur\\Exécuteur");
		$mockExécuteur
			->shouldReceive("exécuter_sys")
			->withArgs(function ($utilisateur, $image, $conteneur, $tests) {
				return $conteneur == "Conteneur de test correct";
			})
			->andReturn([
				"temps_exec" => 0.5,
				"résultats" => [["output" => "Incorrecte", "errors" => "", "code" => 1, "time" => 0.1]],
				"conteneur" => [
					"id" => "leConteneurDeLaNouvelleTentative",
					"ip" => "172.45.2.2",
					"port" => 45667,
				],
			]);
		$mockExécuteur
			->shouldReceive("exécuter_sys")
			->withArgs(function ($utilisateur, $image, $conteneur, $tests) {
				return $conteneur == "Conteneur de test incorrect";
			})
			->andReturn([
				"temps_exec" => 0.65,
				"résultats" => [["output" => "Correcte", "errors" => "", "code" => 0, "time" => 0.2]],
				"conteneur" => [
					"id" => "leConteneurDeLaNouvelleTentative",
					"ip" => "172.45.2.2",
					"port" => 45668,
				],
			]);
		$mockExécuteur
			->shouldReceive("exécuter_sys")
			->withArgs(function ($utilisateur, $image, $conteneur, $tests) {
				return $conteneur == "";
			})
			->andReturn([
				"temps_exec" => 0.65,
				"résultats" => [["output" => "Incorrecte", "errors" => "", "code" => 1, "time" => 0.2]],
				"conteneur" => [
					"id" => "leConteneurDeLaNouvelleTentative",
					"ip" => "172.45.2.2",
					"port" => 45668,
				],
			]);

		//Avancement
		$this->avancement_solution_courte_non_réussie = new Avancement(
			type: "sys",
			tentatives: [$this->tentative_solution_courte_non_réussie],
			titre: "Question non réussie",
			niveau: "Débutant",
		);
		$this->avancement_solution_courte_réussie = new Avancement(
			type: "sys",
			tentatives: [$this->tentative_solution_courte_non_réussie, $this->tentative_solution_courte_réussie],
			titre: "Question réussie",
			niveau: "Débutant",
		);
		$this->avancement_validée_non_réussie = new Avancement(
			type: "sys",
			tentatives: [$this->tentative_validée_non_réussie],
			titre: "Question non réussie",
			niveau: "Débutant",
		);
		$this->avancement_validée_réussie = new Avancement(
			type: "sys",
			tentatives: [$this->tentative_validée_non_réussie, $this->tentative_validée_réussie],
			titre: "Question réussie",
			niveau: "Débutant",
		);

		$mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/nouvelle_question", Mockery::Any())
			->andReturn(null);
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/question_validée_non_réussie", Mockery::Any())
			->andReturn($this->avancement_validée_non_réussie);
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/question_validée_réussie", Mockery::Any())
			->andReturn($this->avancement_validée_réussie);
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/question_solution_courte_non_réussie", Mockery::Any())
			->andReturn($this->avancement_solution_courte_non_réussie);
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/question_solution_courte_réussie", Mockery::Any())
			->andReturn($this->avancement_solution_courte_réussie);

		// User
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->allows("get_user")
			->with("jdoe")
			->andReturn(new User("jdoe"));

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");

		$mockDAOFactory->shouldReceive("get_question_dao")->andReturn($mockQuestionDAO);
		$mockDAOFactory->shouldReceive("get_avancement_dao")->andReturn($mockAvancementDAO);
		$mockDAOFactory->shouldReceive("get_tentative_dao")->andReturn($mockTentativeDAO);
		$mockDAOFactory->shouldReceive("get_exécuteur")->andReturn($mockExécuteur);
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);

		DAOFactory::setInstance($mockDAOFactory);
	}

	// GET
	public function test_étant_donné_une_tentative_existante_lorsquon_appelle_get_on_obtient_la_TentativeSys_et_ses_relations_sous_forme_json()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"GET",
			"/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fc29sdXRpb25fY291cnRlX3JldXNzaWU/1614374491?include=resultats",
		);

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/tentativeCtlTest_sys_réussie.json",
			$résultat_obtenu->getContent(),
		);
	}

	// POST
	public function test_étant_donné_un_avancement_inexistant_et_une_tentative_réussie_lorsquon_appelle_post_lavancement_et_la_tentative_sont_sauvegardés_et_on_obtient_la_TentativeSys_réussie()
	{
		$nouvelle_tentative = new TentativeSys(
			conteneur: [
				"id" => "leConteneurDeLaNouvelleTentative",
				"ip" => "172.45.2.2",
				"port" => 45668,
			],
			réponse: "Bonne réponse",
			date_soumission: 1653690241,
			réussi: true,
			tests_réussis: 1,
			feedback: "Bon travail!",
			temps_exécution: 0,
		);

		$mockTentativeDAO = DAOFactory::getInstance()->get_tentative_dao();
		$mockTentativeDAO
			->shouldReceive("save")
			->withArgs(function ($user, $uri, $t) use ($nouvelle_tentative) {
				if ($t->date_soumission - time() > 1) {
					throw "Temps d'exécution >1s {$t->date_soumission}";
				}
				$t->date_soumission = $nouvelle_tentative->date_soumission;
				return $user == "jdoe" && $uri == "https://depot.com/nouvelle_question" && $t == $nouvelle_tentative;
			})
			->andReturn($nouvelle_tentative);

		$nouvel_avancement = new Avancement(type: "sys", titre: "Question à solution courte", niveau: "Débutant");
		$nouvel_avancement->ajouter_tentative($nouvelle_tentative);

		$mockAvancementDAO = DAOFactory::getInstance()->get_avancement_dao();
		$mockAvancementDAO
			->shouldReceive("save")
			->withArgs(function ($user, $uri, $av) use ($nouvel_avancement) {
				return $user == "jdoe" && $uri == "https://depot.com/nouvelle_question" && $av == $nouvel_avancement;
			})
			->andReturn($nouvel_avancement);

		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vbm91dmVsbGVfcXVlc3Rpb24/tentatives?include=resultats",
			["réponse" => "Bonne réponse"],
		);

		$this->assertEquals(200, $résultat_obtenu->status());

		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/tentativeCtlTest_sys_nouvel_avancement_tentative_réussie.json",
			$résultat_obtenu->getContent(),
		);
	}

	public function test_étant_donné_un_avancement_non_réussi_et_une_tentative_réussie_lorsquon_soumet_la_tentative_lavancement_et_la_tentative_sont_sauvegardés_et_on_obtient_la_TentativeSys_réussie()
	{
		$nouvelle_tentative = new TentativeSys(
			conteneur: [
				"id" => "leConteneurDeLaNouvelleTentative",
				"ip" => "172.45.2.2",
				"port" => 45668,
			],
			réponse: "Bonne réponse",
			date_soumission: 1653690241,
			réussi: true,
			tests_réussis: 1,
			feedback: "Bon travail!",
			temps_exécution: 0,
		);

		$mockTentativeDAO = DAOFactory::getInstance()->get_tentative_dao();
		$mockTentativeDAO
			->shouldReceive("save")
			->withArgs(function ($user, $uri, $t) use ($nouvelle_tentative) {
				if ($t->date_soumission - time() > 1) {
					throw "Temps d'exécution >1s {$t->date_soumission}";
				}
				$t->date_soumission = $nouvelle_tentative->date_soumission;
				return $user == "jdoe" &&
					$uri == "https://depot.com/question_solution_courte_non_réussie" &&
					$t == $nouvelle_tentative;
			})
			->andReturn($nouvelle_tentative);

		$nouvel_avancement = new Avancement(type: "sys", titre: "Question à solution courte", niveau: "Débutant");
		$nouvel_avancement->ajouter_tentative($this->tentative_solution_courte_non_réussie);
		$nouvel_avancement->ajouter_tentative($nouvelle_tentative);

		$mockAvancementDAO = DAOFactory::getInstance()->get_avancement_dao();
		$mockAvancementDAO
			->shouldReceive("save")
			->withArgs(function ($user, $uri, $av) use ($nouvel_avancement) {
				return $user == "jdoe" &&
					$uri == "https://depot.com/question_solution_courte_non_réussie" &&
					$av == $nouvel_avancement;
			})
			->andReturn($nouvel_avancement);

		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fc29sdXRpb25fY291cnRlX25vbl9yw6l1c3NpZQ/tentatives?include=resultats",
			["réponse" => "Bonne réponse"],
		);

		$this->assertEquals(200, $résultat_obtenu->status());

		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/tentativeCtlTest_sys_avancement_non_réussi_tentative_réussie.json",
			$résultat_obtenu->getContent(),
		);
	}

	public function test_étant_donné_un_avancement_non_réussi_pour_une_QuestionSys_et_une_tentative_non_réussie_lorsquon_soumet_la_tentative_lavancement_et_la_tentative_sont_sauvegardés_et_on_obtient_la_TentativeSys_échouée()
	{
		$nouvelle_tentative = new TentativeSys(
			conteneur: [
				"id" => "leConteneurDeLaNouvelleTentative",
				"ip" => "172.45.2.2",
				"port" => 45668,
			],
			réponse: "Mauvaise réponse",
			date_soumission: 1653690241,
			réussi: false,
			tests_réussis: 0,
			feedback: "Encore un effort!",
			temps_exécution: 0,
			résultats: [],
		);

		$mockTentativeDAO = DAOFactory::getInstance()->get_tentative_dao();
		$mockTentativeDAO
			->shouldReceive("save")
			->withArgs(function ($user, $uri, $t) use ($nouvelle_tentative) {
				if ($t->date_soumission - time() > 1) {
					throw "Temps d'exécution >1s {$t->date_soumission}";
				}
				$t->date_soumission = $nouvelle_tentative->date_soumission;
				return $user == "jdoe" &&
					$uri == "https://depot.com/question_solution_courte_non_réussie" &&
					$t == $nouvelle_tentative;
			})
			->andReturn($nouvelle_tentative);

		$nouvel_avancement = new Avancement(type: "sys", titre: "Question à solution courte", niveau: "Débutant");
		$nouvel_avancement->ajouter_tentative($this->tentative_solution_courte_non_réussie);
		$nouvel_avancement->ajouter_tentative($nouvelle_tentative);

		$mockAvancementDAO = DAOFactory::getInstance()->get_avancement_dao();
		$mockAvancementDAO
			->shouldReceive("save")
			->withArgs(function ($user, $uri, $av) use ($nouvel_avancement) {
				return $user == "jdoe" &&
					$uri == "https://depot.com/question_solution_courte_non_réussie" &&
					$av == $nouvel_avancement;
			})
			->andReturn($nouvel_avancement);

		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fc29sdXRpb25fY291cnRlX25vbl9yw6l1c3NpZQ/tentatives?include=resultats",
			["réponse" => "Mauvaise réponse"],
		);

		$this->assertEquals(200, $résultat_obtenu->status());

		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/tentativeCtlTest_sys_avancement_non_réussi_tentative_non_réussie.json",
			$résultat_obtenu->getContent(),
		);
	}

	//Cas d'erreur
	function test_étant_donné_une_question_à_réponse_courte_lorsquon_soumet_une_tentative_sans_réponse_lavancement_et_la_tentative_ne_sont_pas_sauvegardées_et_on_obtient_une_erreur_400()
	{
		$mockTentativeDAO = DAOFactory::getInstance()->get_tentative_dao();
		$mockTentativeDAO->shouldNotReceive("save");

		$mockAvancementDAO = DAOFactory::getInstance()->get_avancement_dao();
		$mockAvancementDAO->shouldNotReceive("save");

		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fc29sdXRpb25fY291cnRlX25vbl9yw6l1c3NpZQ/tentatives?include=resultats",
			[],
		);

		$this->assertEquals(400, $résultat_obtenu->status());
	}
}
