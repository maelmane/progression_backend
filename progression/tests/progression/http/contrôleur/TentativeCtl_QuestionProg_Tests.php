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
use progression\domaine\entité\{
	Avancement,
	TestProg,
	Exécutable,
	Question,
	TentativeProg,
	Commentaire,
	QuestionProg,
	User,
};

use Illuminate\Auth\GenericUser;

final class TentativeCtl_QuestionProg_Tests extends ContrôleurTestCase
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

		// QuestionProg
		//aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fcsOpdXNzaWU
		$question_réussie = new QuestionProg(
			titre: "Question réussie",
			niveau: "Débutant",
			feedback_pos: "Bon travail!",
			feedback_neg: "Encore un effort!",
			feedback_err: "oups!",
			exécutables: [
				// Ébauches
				"python" => new Exécutable("#+TODO\nprint(\"Hello world!\")", "python"),
				"java" => new Exécutable("//+TODO\nSystem.out.println(\"Hello world!\")", "java"),
			],
			// TestsProg
			tests: [
				new TestProg("2 salutations", "Bonjour\nBonjour\n", "2", "", "C'est ça!", "C'est pas ça :(", "arrrg!"),
			],
		);

		//aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fbm9uX3LDqXVzc2ll
		$question_non_réussie = new QuestionProg(
			titre: "Question non réussie",
			niveau: "Débutant",
			feedback_pos: "Bon travail!",
			feedback_neg: "Encore un effort!",
			feedback_err: "oups!",
			exécutables: [
				// Ébauches
				"python" => new Exécutable("#+TODO\nprint(\"Hello world!\")", "python"),
				"java" => new Exécutable("//+TODO\nSystem.out.println(\"Hello world!\")", "java"),
			],
			// TestsProg
			tests: [
				new TestProg("2 salutations", "Bonjour\nBonjour\n", "2", "", "C'est ça!", "C'est pas ça :(", "arrrg!"),
			],
		);

		$mockQuestionDAO = Mockery::mock("progression\\dao\\question\\QuestionDAO");
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/question_réussie")
			->andReturn($question_réussie);
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/question_non_réussie")
			->andReturn($question_non_réussie);
		$mockQuestionDAO->shouldReceive("get_question")->andReturn(null);

		// Tentative
		// Tentative réussie
		$this->tentative_réussie = new TentativeProg(
			langage: "python",
			code: "codeTest",
			date_soumission: "1614374490",
			réussi: true,
			résultats: [],
			tests_réussis: 2,
			feedback: "feedbackTest",
			temps_exécution: 5,
		);

		// Tentative non réussie
		$this->tentative_non_réussie = new TentativeProg(
			langage: "python",
			code: "codeTest",
			date_soumission: "1614374490",
			réussi: false,
			résultats: [],
			tests_réussis: 0,
			feedback: "feedbackTest",
			temps_exécution: 5,
		);

		$mockTentativeDAO = Mockery::mock("progression\\dao\\tentative\\TentativeDAO");
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "https://depot.com/question_réussie", "1614374490")
			->andReturn($this->tentative_réussie);
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "https://depot.com/question_non_réussie", "1614374490")
			->andReturn($this->tentative_non_réussie);
		$mockTentativeDAO->shouldReceive("get_tentative")->andReturn(null);

		//$mockTentativeDAO->shouldReceive("save")->andReturnArg(2);

		// Commentaire
		$commentaire = new Commentaire(99, "le 99iem message", "mock", 1615696276, 14);
		$mockCommentaireDAO = Mockery::mock("progression\\dao\\CommentaireDAO");
		$mockCommentaireDAO
			->shouldReceive("get_commentaires_par_tentative")
			->with("jdoe", "https://depot.com/question_réussie", 1614374490)
			->andReturn($commentaire);

		// Exécuteur
		$mockExécuteur = Mockery::mock("progression\\dao\\exécuteur\\Exécuteur");
		$mockExécuteur
			->shouldReceive("exécuter_prog")
			->withArgs(function ($exec, $test) {
				return $exec->lang == "python";
			})
			->andReturn([
				"temps_exec" => 0.551,
				"résultats" => [["output" => "Bonjour\nAllo\n", "errors" => "", "time" => 0.03]],
			]);
		$mockExécuteur
			->shouldReceive("exécuter_prog")
			->withArgs(function ($exec, $test) {
				return $exec->lang == "java";
			})
			->andThrow(new ExécutionException("Erreur test://TentativeCtlTests.php"));

		$mockExécuteur
			->shouldReceive("exécuter_prog")
			->withArgs(function ($exec, $test) {
				return $exec->lang == "tentativeRéussie";
			})
			->andReturn([
				"temps_exec" => 0.551,
				"résultats" => [["output" => "Bonjour\nBonjour\n", "errors" => "", "time" => 0.03]],
			]);

		//Avancement

		// Avancement réussi
		$this->avancement_réussi = new Avancement(Question::ETAT_REUSSI, Question::TYPE_PROG, [
			new TentativeProg("python", "codeTest", 1614965817, true, 2, "feedbackTest"),
		]);

		// Avancement non réussi
		$this->avancement_non_réussi = new Avancement(Question::ETAT_NONREUSSI, Question::TYPE_PROG, [
			new TentativeProg("python", "codeTest", 1614965817, false, 2, "feedbackTest"),
		]);

		$mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/question_réussie")
			->andReturn($this->avancement_réussi);

		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/question_non_réussie")
			->andReturn($this->avancement_non_réussi);

		$mockAvancementDAO->allows("save")->andReturnArg(2);

		// User
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->allows("get_user")
			->with("jdoe")
			->andReturn(new User("jdoe"));

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_tentative_dao")->andReturn($mockTentativeDAO);
		$mockDAOFactory->shouldReceive("get_commentaire_dao")->andReturn($mockCommentaireDAO);
		$mockDAOFactory->shouldReceive("get_avancement_dao")->andReturn($mockAvancementDAO);
		//$mockDAOFactory->shouldReceive("get_tentative_prog_dao")->andReturn($mockTentativeDAO);
		$mockDAOFactory->shouldReceive("get_question_dao")->andReturn($mockQuestionDAO);
		$mockDAOFactory->shouldReceive("get_exécuteur")->andReturn($mockExécuteur);
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);

		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
		DAOFactory::setInstance(null);
	}
	/*
	   public function test_étant_donné_une_tentative_existante_lorsquon_appelle_get_on_obtient_la_TentativeProg_et_ses_relations_sous_forme_json()
	   {
	   $résultat_obtenu = $this->actingAs($this->user)->call(
	   "GET",
	   "/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fcmV1c3NpZQ/1614374490",
	   );

	   $this->assertEquals(200, $résultat_obtenu->status());
	   $this->assertJsonStringEqualsJsonFile(
	   __DIR__ . "/résultats_attendus/tentativeCtlTest_2.json",
	   $résultat_obtenu->getContent(),
	   );
	   }

	   public function test_étant_donné_une_tentative_inexistante_lorsquon_appelle_get_on_obtient_une_erreur_404()
	   {
	   $résultat_obtenu = $this->actingAs($this->user)->call(
	   "GET",
	   "/tentative/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fcmV1c3NpZQ/9999999999",
	   );

	   $this->assertEquals(404, $résultat_obtenu->status());
	   $this->assertEquals('{"erreur":"Ressource non trouvée."}', $résultat_obtenu->getContent());
	   }

	   public function test_étant_donné_une_tentative_réussie_et_un_avancement_réussi_lorsquon_appelle_post_lavancement_et_la_tentative_sont_sauvegardés_et_on_obtient_la_TentativeProg_réussie(){
	   $heure_tentative = 0;
	   $nouvelle_tentative = new TentativeProg(
	   langage: "tentativeRéussie",
	   code: "#+TODO\nprint(\"Hello world!\")",
	   date_soumission: $heure_tentative,
	   réussi: true,
	   tests_réussis: 2,
	   feedback: "Bon travail!",
	   );

	   $nouvel_avancement = new Avancement(
	   etat: Question::ETAT_REUSSI,
	   type: Question::TYPE_PROG,
	   tentatives: [
	   $this->tentative_réussie,
	   $nouvelle_tentative,
	   ],
	   titre: "appeler_une_fonction_paramétrée",
	   niveau: "Débutant"
	   );

	   $mockAvancementDAO = DAOFactory::getInstance()->get_avancement_dao();
	   $mockAvancementDAO
	   ->shouldReceive("save")
	   ->once()
	   ->withArgs(function ($user, $uri, $av) use ($nouvel_avancement) {
	   return $user == "jdoe" &&
	   $uri == "https://depot.com/questions_réussie" &&
	   $av == $nouvel_avancement;
	   })
	   ->andReturn($nouvel_avancement);

	   $mockTentativeDAO = DAOFactory::getInstance()->get_tentative_dao();
	   $mockTentativeDAO
	   ->shouldReceive("save")
	   ->once()
	   ->withArgs(function ($user, $uri, $t) use ($nouvelle_tentative) {
	   return $user == "jdoe" &&
	   $uri == "https://depot.com/questions_réussie" &&
	   $t == $nouvelle_tentative;
	   })
	   ->andReturn($nouvelle_tentative);

	   $résultat_obtenu = $this->actingAs($this->user)->call(
	   "POST",
	   "/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fcul1c3NpZQ/tentatives?include=resultats",
	   ["langage" => "tentativeRéussie", "code" => "#+TODO\nprint(\"Hello world!\")"],
	   );

	   $heure_courante = time();
	   $heure_tentative = json_decode($résultat_obtenu->getContent())->data->attributes->date_soumission;
	   $this->assertEquals(200, $résultat_obtenu->status());
	   $this->assertLessThanOrEqual(
	   1,
	   $heure_courante - $heure_tentative,
	   "Heure courante: {$heure_courante}, Heure tentative: {$heure_tentative}",
	   );

	   $this->assertJsonStringEqualsJsonString(
	   sprintf(file_get_contents(__DIR__ . "/résultats_attendus/tentativeCtlTest_réussi.json"), $heure_tentative),
	   $résultat_obtenu->getContent(),
	   );
	   }

	   public function test_étant_donné_une_tentative_réussie_et_un_avancement_non_réussi_lorsquon_appelle_post_lavancement_et_la_tentative_sont_sauvegardés_et_on_obtient_la_TentativeProg_réussie(){
	   $résultat_obtenu = $this->actingAs($this->user)->call(
	   "POST",
	   "/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fbm9uX3LpdXNzaWU/tentatives?include=resultats",
	   ["langage" => "tentativeRéussie", "code" => "#+TODO\nprint(\"Hello world!\")"],
	   );
	   
	   $heure_courante = time();
	   $heure_tentative = json_decode($résultat_obtenu->getContent())->data->attributes->date_soumission;

	   $nouvelle_tentative = new TentativeProg(
	   langage: "tentativeRéussie",
	   code: "#+TODO\nprint(\"Hello world!\")",
	   date_soumission: $heure_tentative,
	   réussi: true,
	   tests_réussis: 2,
	   feedback: "Bon travail!",
	   );

	   $nouvel_avancement = new Avancement(
	   etat: Question::ETAT_REUSSI,
	   type: Question::TYPE_PROG,
	   tentatives: [
	   $this->tentative_non_réussie,
	   $nouvelle_tentative
	   ],
	   );

	   $mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
	   $mockAvancementDAO->shouldReceive("save")
	   ->withArgs(function ($user, $uri, $t) use ($nouvel_avancement) {
	   return $user == "jdoe" &&
	   $uri == "https://depot.com/question_non_réussie" &&
	   $av == $nouvel_avancement;
	   })
	   ->andReturn($nouvel_avancement);

	   $mockTentativeDAO = Mockery::mock("progression\\dao\\tentative\\TentativeDAO");
	   $mockTentativeDAO->shouldReceive("save")
	   ->withArgs(function ($user, $uri, $tentative) use ($nouvelle_tentative) {
	   return $user == "jdoe" &&
	   $uri == "https://depot.com/question_non_réussie" &&
	   $tentative == $nouvelle_tentative;
	   })
	   ->andReturn($nouvelle_tentative);

	   $this->assertEquals(200, $résultat_obtenu->status());
	   $this->assertLessThanOrEqual(
	   1,
	   $heure_courante - $heure_tentative,
	   "Heure courante: {$heure_courante}, Heure tentative: {$heure_tentative}",
	   );

	   $this->assertJsonStringEqualsJsonString(
	   sprintf(file_get_contents(__DIR__ . "/résultats_attendus/tentativeCtlTest_non_réussi.json"), $heure_tentative),
	   $résultat_obtenu->getContent(),
	   );
	   }
	 */
	public function test_étant_une_tentative_non_réussie_et_un_avancement_non_réussi_lorsquon_appelle_post_lavancement_et_la_tentative_sont_sauvegardés_et_on_obtient_la_TentativeProg_non_réussie()
	{
		$nouvelle_tentative = new TentativeProg(
			langage: "python",
			code: "#+TODO\nprint(\"Hello world!\")",
			date_soumission: 1653496098,
			réussi: false,
			tests_réussis: 2,
			feedback: "feedbackTest",
		);

		$nouvel_avancement = new Avancement(
			etat: Question::ETAT_NONREUSSI,
			type: Question::TYPE_PROG,
			tentatives: [$this->tentative_non_réussie, $nouvelle_tentative],
		);

		$mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
		$mockAvancementDAO
			->shouldReceive("save")
			->withArgs(function ($user, $uri, $av) use ($nouvel_avancement) {
				return $user == "jdoe" && $uri == "https://depot.com/question_non_réussie";
			})
			->andReturn($nouvel_avancement);

		$mockTentativeDAO = Mockery::mock("progression\\dao\\tentative\\TentativeDAO");
		$mockTentativeDAO
			->shouldReceive("save")
			->withArgs(function ($user, $uri, $tentative) use ($nouvelle_tentative) {
				return $user == "jdoe" &&
					$uri == "https://depot.com/question_non_réussie" &&
					$tentative == $nouvelle_tentative;
			})
			->andReturn($nouvelle_tentative);

		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fbm9uX3LDqXVzc2ll/tentatives?include=resultats",
			["langage" => "python", "code" => "#+TODO\nprint(\"Hello world!\")"],
		);

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertJsonFileEqualsJsonString(
			__DIR__ . "/résultats_attendus/tentativeCtlTest_1.json",
			$résultat_obtenu->getContent(),
		);
	}
	/*
	   public function test_étant_donné_une_tentative_sans_code_lorsquelle_est_soumise_on_obtient_une_erreur_de_validation()
	   {
	   $résultat_obtenu = $this->actingAs($this->user)->call(
	   "POST",
	   "/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fcul1c3NpZQ/tentatives",
	   ["langage" => "python"],
	   );

	   $this->assertEquals(400, $résultat_obtenu->status());
	   $this->assertEquals('{"erreur":{"code":["Le champ code est obligatoire."]}}', $résultat_obtenu->getContent());
	   }
	   
	   public function test_étant_donné_un_url_de_compilebox_inaccessible_lorsquon_appelle_post_on_obtient_Service_non_disponible()
	   {
	   $résultat_obtenu = $this->actingAs($this->user)->call(
	   "POST",
	   "/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fcul1c3NpZQ/tentatives",
	   ["langage" => "java", "code" => "#+TODO\nprint(\"on ne se rendra pas à exécuter ceci\")"],
	   );

	   $this->assertEquals(503, $résultat_obtenu->status());
	   $this->assertEquals('{"erreur":"Service non disponible."}', $résultat_obtenu->getContent());
	   }

	   public function test_étant_donné_une_tentative_avec_un_code_sans_TODO_lorsquelle_est_soumise_on_obtient_Tentative_intraitable()
	   {
	   $résultat_obtenu = $this->actingAs($this->user)->call(
	   "POST",
	   "/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fcul1c3NpZQ/tentatives",
	   ["langage" => "python", "code" => "print(\"Hello world!\")"],
	   );

	   $this->assertEquals(400, $résultat_obtenu->status());
	   $this->assertEquals('{"erreur":"Tentative intraitable."}', $résultat_obtenu->getContent());
	   }

	   public function test_étant_donné_un_avancement_non_réussi_et_une_tentative_avec_un_test_unique_lorsquelle_est_soumise_lavancement_et_la_tentative_ne_sont_pas_sauvegardés_et_obtient_la_TentativeProg()
	   {
	   $résultat_obtenu = $this->actingAs($this->user)->call(
	   "POST",
	   "/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fcul1c3NpZQ/tentatives?include=resultats",
	   [
	   "langage" => "python",
	   "code" => "#+TODO\nprint(\"Hello world!\")",
	   "test" => ["nom" => "Test bonjour", "sortie_attendue" => "bonjour", "entrée" => "bonjour"],
	   ],
	   );
	   $mockTentativeDAO = Mockery::mock("progression\\dao\\tentative\\TentativeProgDAO");
	   $mockTentativeDAO->shouldNotReceive("save")->withAnyArgs();

	   $mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
	   $mockAvancementDAO->shouldNotReceive("save")->withAnyArgs();

	   $this->assertEquals(200, $résultat_obtenu->status());

	   $heure_courante = time();
	   $heure_tentative = json_decode($résultat_obtenu->getContent())->data->attributes->date_soumission;
	   $this->assertLessThanOrEqual(
	   1,
	   $heure_courante - $heure_tentative,
	   "Heure courante: {$heure_courante}, Heure tentative: {$heure_tentative}",
	   );

	   $this->assertJsonStringEqualsJsonString(
	   sprintf(file_get_contents(__DIR__ . "/résultats_attendus/tentativeCtlTest_3.json"), $heure_tentative),
	   $résultat_obtenu->getContent(),
	   );
	   }

	   public function test_étant_donné_une_tentative_ayant_du_code_dépassant_la_taille_maximale_de_caractères_on_obtient_une_erreur_400()
	   {
	   $_ENV["TAILLE_CODE_MAX"] = 23;
	   $testCode = "#+TODO\n日本語でのテストです\n#-TODO"; //24 caractères UTF8

	   $mockTentativeDAO = DAOFactory::getInstance()->get_tentative_dao();
	   $mockTentativeDAO->shouldNotReceive("save")->withAnyArgs();

	   $résultat_obtenu = $this->actingAs($this->user)->call("POST", "/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fcul1c3NpZQ/tentatives", [
	   "langage" => "python",
	   "code" => "$testCode",
	   ]);

	   $this->assertEquals(400, $résultat_obtenu->status());
	   $this->assertEquals('{"erreur":{"code":["Le code soumis 24 > 23 caractères."]}}', $résultat_obtenu->getContent());
	   }

	   public function test_étant_donné_une_tentative_ayant_exactement_la_taille_maximale_de_caractères_on_obtient_un_code_200()
	   {
	   $_ENV["TAILLE_CODE_MAX"] = 24;
	   $testCode = "#+TODO\n日本語でのテストです\n#-TODO"; //24 caractères UTF8

	   $mockTentativeDAO = DAOFactory::getInstance()->get_tentative_dao();
	   $mockTentativeDAO->shouldReceive("save")->andReturnArg(2);

	   $résultat_obtenu = $this->actingAs($this->user)->call(
	   "POST",
	   "/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fcul1c3NpZQ/tentatives",
	   ["langage" => "python", "code" => "$testCode"],
	   );

	   $this->assertEquals(200, $résultat_obtenu->status());
	   }
	 */
}
