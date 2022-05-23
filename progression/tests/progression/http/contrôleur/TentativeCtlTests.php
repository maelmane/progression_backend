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
	TestSys,
	Exécutable,
	Question,
	QuestionSys,
	TentativeProg,
	TentativeSys,
	Commentaire,
	QuestionProg,
	User,
};

use Illuminate\Auth\GenericUser;

final class TentativeCtlTests extends ContrôleurTestCase
{
	public $user;
	public $headers;
	protected static $ancienne_tentative;
	protected static $ancienne_tentative_sys;
	protected static $questionSys1;
	protected static $questionSys2;
	protected static $questionSys3;
	protected static $questionSys4;
	protected static $questionSys5;
	protected static $questionSys6;
	protected static $questionSys7;
	protected static $questionSys8;
	protected static $questionSys10;

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
		$tentative->temps_exécution = 5;

		$mockTentativeDAO = Mockery::mock("progression\\dao\\tentative\\TentativeDAO");

		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "prog1/les_fonctions_01/appeler_une_fonction_paramétrée", "9999999999")
			->andReturn(null);
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "prog1/les_fonctions_01/appeler_une_fonction_paramétrée", "1614374490")
			->andReturn($tentative);
		$mockTentativeDAO->shouldReceive("save")->andReturnArg(2);

		$mockTentativeDAO
			->shouldReceive("get_toutes")
			->with("jdoe", "https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions")
			->andReturn([
				new TentativeSys(
					conteneur: ["id" => "leConteneurDeLancienneTentative",
										"ip" => "192.168.0.1",
										"port" => 12345 ],
					réponse: "laRéponseDeLancienneTentative",
					date_soumission: "1614374490",
				),
				new TentativeSys(
					conteneur: ["id" => "leConteneurDeLancienneTentative2",
										"ip" => "192.168.0.1",
										"port" => 12345 ],
					réponse: "laRéponseDeLancienneTentative2",
					date_soumission: "1614374491",
				),
			]);

		$mockTentativeDAO
			->shouldReceive("get_dernière")
			->with("jdoe", "https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions")
			->andReturn(
				new TentativeSys(
					conteneur: ["id" => "leConteneurDeLancienneTentative7"],
					réponse: "laRéponseDeLancienneTentative7",
					date_soumission: "1614374491",
				),
			);
		
		// Commentaire
		$commentaire = new Commentaire(99, "le 99iem message", "mock", 1615696276, 14);

		$mockCommentaireDAO = Mockery::mock("progression\\dao\\CommentaireDAO");

		$mockCommentaireDAO
			->shouldReceive("get_commentaires_par_tentative")
			->with("jdoe", "prog1/les_fonctions_01/appeler_une_fonction_paramétrée", 1614374490)
			->andReturn($commentaire);

		// QuestionProg
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
		$question->exécutables["tentativeRéussie"] = new Exécutable(
			"#+TODO\nprint(\"Hello world!\")",
			"tentativeRéussie",
		);
		// TestsProg
		$question->tests = [
			new TestProg("2 salutations", "Bonjour\nBonjour\n", "2", "", "C'est ça!", "C'est pas ça :(", "arrrg!"),
		];

		$mockQuestionDAO = Mockery::mock("progression\\dao\\question\\QuestionDAO");
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($question);
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
		$avancement = new Avancement(Question::ETAT_REUSSI, Question::TYPE_PROG, [
			new TentativeProg("python", "codeTest", 1614965817, false, 2, "feedbackTest"),
		]);
		$mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($avancement);

		$mockAvancementDAO->allows("save")->andReturn($avancement);

		// User
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->allows("get_user")
			->with("jdoe")
			->andReturn(new User("jdoe"));

		// TentativeSys
		$tentative = new TentativeSys(["id"=>"leConteneur"], "~laRéponse~", "1614374490");
		$tentative->tests_réussis = 1;
		$tentative->réussi = true;
		$tentative->feedback = "feedbackTest";
		$tentative->temps_exécution = 5;

		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with(
				"jdoe",
				"https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions",
				"1614374490",
			)
			->andReturn($tentative);
		$mockTentativeDAO->shouldReceive("save")->andReturnArg(2);

		$mockCommentaireDAO
			->shouldReceive("get_commentaires_par_tentative")
			->with(
				"jdoe",
				"https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions",
				"1614374490",
			)
			->andReturn($commentaire);

		// QuestionSys avec solution courte
		self::$questionSys1 = new QuestionSys();
		self::$questionSys1->type = Question::TYPE_SYS;
		self::$questionSys1->nom = "toutes_les_permissions";
		self::$questionSys1->solution = "~laSolution~";
		self::$questionSys1->uri =
			"https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions";
		self::$questionSys1->feedback_pos = "Bon travail!";
		self::$questionSys1->feedback_neg = "Encore un effort!";

		$mockExécuteur
			->shouldReceive("exécuter_sys")
			->withArgs(function ($question, $tentative) {
				return $question == self::$questionSys1;
			})
			->andReturn([
				"temps_exec" => 0.5,
				"résultats" => [["output" => "Incorrecte", "time" => 0.1]],
				"conteneur" => ["id" => "leConteneurDeLaNouvelleTentative", "ip" => "172.45.2.2", "port" => 45667],
			]);
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions")
			->andReturn(self::$questionSys1);

		//AvancementSys
		$avancement = new Avancement(Question::ETAT_REUSSI, Question::TYPE_SYS, [
			new TentativeSys(["id"=>"leConteneur"], "~laRéponse~", 1614965817, false, 2, "feedbackTest"),
		]);

		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions")
			->andReturn($avancement);

		$mockAvancementDAO->allows("save")->andReturn($avancement);

		//QuestionSys avec validations
		self::$questionSys3 = new QuestionSys();
		self::$questionSys3->type = Question::TYPE_SYS;
		self::$questionSys3->nom = "toutes_les_permissions_2";
		self::$questionSys3->uri =
			"https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions_2";
		self::$questionSys3->feedback_pos = "Bon travail!";
		self::$questionSys3->feedback_neg = "Encore un effort!";
		self::$questionSys3->tests = [
			new TestSys(
				nom: "Toutes permissions 3",
				sortie_attendue: "-rwxrwxrwx",
				validation: "laValidation",
				utilisateur: "momo",
				feedback_pos: "yes!",
				feedback_neg: "non!",
			),
		];

		$mockExécuteur
			->shouldReceive("exécuter_sys")
			->withArgs(function ($question, $tentative) {
				return $question == self::$questionSys3;
			})
			->andReturn([
				"temps_exec" => 0.5,
				"résultats" => [["output" => "Incorrecte", "time" => 0.1]],
				"conteneur" => ["id" => "leConteneurDeLancienneTentative3", "ip" => "172.45.2.2", "port" => 45667],
			]);

		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions_2")
			->andReturn(self::$questionSys3);

		//AvancementSys 3
		$avancement = new Avancement(Question::ETAT_NONREUSSI, Question::TYPE_SYS, [
			new TentativeSys(["id"=>"leConteneur"], 1614965817, false, 2, "feedbackTest"),
		]);
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions3")
			->andReturn($avancement);

		$mockAvancementDAO->allows("save")->andReturn($avancement);

		$mockExécuteur
			->shouldReceive("exécuter_sys")
			->withArgs(function ($question, $tentative) {
				return $question == self::$questionSys4;
			})
			->andReturn([
				"temps_exec" => 0.5,
				"résultats" => [["output" => "Incorrecte", "time" => 0.1]],
				"conteneur" => ["id" => "leConteneurDeLaNouvelleTentative", "ip" => "172.45.2.2", "port" => 45667],
			]);

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_tentative_dao")->andReturn($mockTentativeDAO);
		$mockDAOFactory->shouldReceive("get_commentaire_dao")->andReturn($mockCommentaireDAO);
		$mockDAOFactory->shouldReceive("get_avancement_dao")->andReturn($mockAvancementDAO);
		$mockDAOFactory->shouldReceive("get_tentative_prog_dao")->andReturn($mockTentativeDAO);
		$mockDAOFactory->shouldReceive("get_tentative_sys_dao")->andReturn($mockTentativeDAO);
		$mockDAOFactory->shouldReceive("get_question_dao")->andReturn($mockQuestionDAO);
		$mockDAOFactory->shouldReceive("get_exécuteur")->andReturn($mockExécuteur);
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);

		DAOFactory::setInstance($mockDAOFactory);

		self::$ancienne_tentative = new TentativeProg(
			langage: "python",
			code: "codeTest",
			date_soumission: "1614374490",
		);

		self::$ancienne_tentative_sys = new TentativeSys(
			conteneur: ["id"=>"leConteneurDeLancienneTentative"],
			réponse: "laRéponseDeLancienneTentative",
			date_soumission: "1614374490",
		);
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

	public function test_étant_donné_une_questionProg_le_username_dun_utilisateur_le_chemin_dune_question_le_timestamp_une_tentative_réussie_et_un_avancement_réussi_lorsquon_appelle_post_lavancement_et_la_tentative_sont_sauvegardés_et_on_obtient_la_TentativeProg_avec_ses_résultats_et_ses_relations_sous_forme_json()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/tentatives?include=resultats",
			["langage" => "tentativeRéussie", "code" => "#+TODO\nprint(\"Hello world!\")"],
		);
		$heure_courante = time();
		$heure_tentative = json_decode($résultat_obtenu->getContent())->data->attributes->date_soumission;
		self::$ancienne_tentative->tests_réussis = 2;
		self::$ancienne_tentative->réussi = true;
		self::$ancienne_tentative->feedback = "feedbackTest";
		self::$ancienne_tentative->temps_exécution = 5;
		$ancien_avancement = new Avancement(
			etat: Question::ETAT_REUSSI,
			type: Question::TYPE_PROG,
			tentatives: [self::$ancienne_tentative],
		);

		$nouvel_avancement = new Avancement(
			etat: Question::ETAT_REUSSI,
			type: Question::TYPE_PROG,
			tentatives: [
				self::$ancienne_tentative,
				new TentativeProg(
					langage: "tentativeRéussie",
					code: "#+TODO\nprint(\"Hello world!\")",
					date_soumission: $heure_tentative,
					réussi: true,
					tests_réussis: 2,
					feedback: "Bon travail!",
				),
			],
		);

		$mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
		$mockAvancementDAO
			->shouldReceive("get_avancement")
			->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($ancien_avancement);

		$mockAvancementDAO
			->shouldReceive("save")
			->withArgs(function ($user, $uri, $av) use ($nouvel_avancement) {
				return $user == "jdoe" &&
					   $uri == "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction" &&
					   $av == $nouvel_avancement;
			})
			->andReturn($nouvel_avancement);

		$mockTentativeDAO = Mockery::mock("progression\\dao\\tentative\\TentativeDAO");

		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "prog1/les_fonctions_01/appeler_une_fonction_paramétrée", "1614374490")
			->andReturn(self::$ancienne_tentative);
		$mockTentativeDAO->shouldReceive("save")->andReturnArg(2);

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertLessThan(
			1,
			$heure_courante - $heure_tentative,
			"Heure courante: {$heure_courante}, Heure tentative: {$heure_tentative}",
		);

		$this->assertJsonStringEqualsJsonString(
			sprintf(file_get_contents(__DIR__ . "/résultats_attendus/tentativeCtlTest_4.json"), $heure_tentative),
			$résultat_obtenu->getContent(),
		);
	}

	public function test_étant_donné_une_questionSys_lorsquon_soumet_la_bonne_réponse_lavancement_et_la_tentative_sont_sauvegardés_et_on_obtient_la_TentativeSys_réussie()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3N5cy9wZXJtaXNzaW9uczAxL29jdHJveWVyX3RvdXRlc19sZXNfcGVybWlzc2lvbnM/tentatives?include=resultats",
			["conteneur" => "leConteneurDeLaNouvelleTentative", "réponse" => "laSolution"],
		);

		$mockTentativeDAO = DAOFactory::getInstance()->get_tentative_dao();
		$mockTentativeDAO->shouldReceive("save")->andReturnArg(2);

		$mockAvancementDAO = DAOFactory::getInstance()->get_avancement_dao();
		$mockAvancementDAO->shouldReceive("save")->andReturnArg(2);

		$heure_tentative = json_decode($résultat_obtenu->getContent())->data->attributes->date_soumission;

		$this->assertEquals(200, $résultat_obtenu->status());

		$this->assertJsonStringEqualsJsonString(
			sprintf(file_get_contents(__DIR__ . "/résultats_attendus/tentativeCtlTest_5.json"), $heure_tentative),
			$résultat_obtenu->getContent(),
		);
	}

	public function test_étant_donné_une_questionSys_lorsquon_soumet_la_mauvaise_réponse_lavancement_et_la_tentative_sont_sauvegardés_et_on_obtient_la_TentativeSys_échouée()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3N5cy9wZXJtaXNzaW9uczAxL29jdHJveWVyX3RvdXRlc19sZXNfcGVybWlzc2lvbnM/tentatives?include=resultats",
			["conteneur" => "leConteneurDeLaNouvelleTentative6", "réponse" => "Bonsoir"],
		);

		$mockTentativeDAO = DAOFactory::getInstance()->get_tentative_dao();
		$mockTentativeDAO->shouldReceive("save")->andReturnArg(2);

		$mockAvancementDAO = DAOFactory::getInstance()->get_avancement_dao();
		$mockAvancementDAO->shouldReceive("save")->andReturnArg(2);

		$heure_tentative = json_decode($résultat_obtenu->getContent())->data->attributes->date_soumission;

		$this->assertEquals(200, $résultat_obtenu->status());

		$this->assertJsonStringEqualsJsonString(
			sprintf(file_get_contents(__DIR__ . "/résultats_attendus/tentativeCtlTest_6.json"), $heure_tentative),
			$résultat_obtenu->getContent(),
		);
	}

	public function test_étant_donné_une_question_sys_avec_tests_de_validation_lorsquon_soumet_une_tentative_réussie_lavancement_et_la_tentative_sont_sauvegardée_et_on_obtient_une_TentativeSys_réussie(){
	}
	
	public function test_étant_donné_une_question_sys_avec_tests_de_validation_lorsquon_soumet_une_tentative_échouée_lavancement_et_la_tentative_sont_sauvegardée_et_on_obtient_une_TentativeSys_échouée(){
	}

	
	public function test_étant_donné_le_username_dun_utilisateur_le_chemin_dune_question_le_timestamp_une_tentative_réussie_et_un_avancement_non_réussi_lorsquon_appelle_post_lavancement_et_la_tentative_sont_sauvegardés_et_on_obtient_la_TentativeProg_avec_ses_résultats_et_ses_relations_sous_forme_json()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/tentatives?include=resultats",
			["langage" => "tentativeRéussie", "code" => "#+TODO\nprint(\"Hello world!\")"],
		);
		$heure_courante = time();
		$heure_tentative = json_decode($résultat_obtenu->getContent())->data->attributes->date_soumission;
		self::$ancienne_tentative = new TentativeProg(
			langage: "python",
			code: "codeTest",
			date_soumission: "1614374490",
		);
		self::$ancienne_tentative->tests_réussis = 0;
		self::$ancienne_tentative->réussi = false;
		self::$ancienne_tentative->feedback = "feedbackTest";
		self::$ancienne_tentative->temps_exécution = 5;
		$ancien_avancement = new Avancement(
			etat: Question::ETAT_NONREUSSI,
			type: Question::TYPE_PROG,
			tentatives: [self::$ancienne_tentative],
		);

		$nouvel_avancement = new Avancement(
			etat: Question::ETAT_REUSSI,
			type: Question::TYPE_PROG,
			tentatives: [
				self::$ancienne_tentative,
				new TentativeProg(
					langage: "tentativeRéussie",
					code: "#+TODO\nprint(\"Hello world!\")",
					date_soumission: $heure_tentative,
					réussi: true,
					tests_réussis: 2,
					feedback: "Bon travail!",
				),
			],
		);

		$mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
		$mockAvancementDAO
	   ->shouldReceive("get_avancement")
	   ->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
	   ->andReturn($ancien_avancement);

		$mockAvancementDAO
	   ->shouldReceive("save")
	   ->withArgs(function ($user, $uri, $av) use ($nouvel_avancement) {
		   return $user == "jdoe" &&
				  $uri == "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction" &&
				  $av == $nouvel_avancement;
	   })
	   ->andReturn($nouvel_avancement);

		$mockTentativeDAO = Mockery::mock("progression\\dao\\tentative\\TentativeDAO");

		$mockTentativeDAO
	   ->shouldReceive("get_tentative")
	   ->with("jdoe", "prog1/les_fonctions_01/appeler_une_fonction_paramétrée", "1614374490")
	   ->andReturn(self::$ancienne_tentative);
		$mockTentativeDAO->shouldReceive("save")->andReturnArg(2);

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertLessThan(
			1,
			$heure_courante - $heure_tentative,
			"Heure courante: {$heure_courante}, Heure tentative: {$heure_tentative}",
		);

		$this->assertJsonStringEqualsJsonString(
			sprintf(file_get_contents(__DIR__ . "/résultats_attendus/tentativeCtlTest_4.json"), $heure_tentative),
			$résultat_obtenu->getContent(),
		);
	}

	public function test_étant_donné_le_username_dun_utilisateur_le_chemin_dune_question_le_timestamp_et_une_tentative_non_réussie_et_un_avancement_non_réussi_lorsquon_appelle_post_lavancement_et_la_tentative_sont_sauvegardés_et_on_obtient_la_TentativeProg_avec_ses_résultats_et_ses_relations_sous_forme_json()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/tentatives?include=resultats",
			["langage" => "python", "code" => "#+TODO\nprint(\"Hello world!\")"],
		);
		$heure_courante = time();
		$heure_tentative = json_decode($résultat_obtenu->getContent())->data->attributes->date_soumission;

		$nouvel_avancement = new Avancement(Question::ETAT_NONREUSSI, Question::TYPE_PROG, [
			new TentativeProg("python", "#+TODO\nprint(\"Hello world!\")", $heure_tentative, false, 2, "feedbackTest"),
		]);

		$mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
		$mockAvancementDAO
	   ->shouldReceive("get_avancement")
	   ->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
	   ->andReturn(null);

		$mockAvancementDAO
	   ->shouldReceive("save")
	   ->withArgs(function ($user, $uri, $av) use ($nouvel_avancement) {
		   return $user == "jdoe" &&
				  $uri == "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction" &&
				  $av == $nouvel_avancement;
	   })
	   ->andReturn($nouvel_avancement);

		$mockTentativeDAO = Mockery::mock("progression\\dao\\tentative\\TentativeDAO");

		$mockTentativeDAO->shouldNotReceive("get_tentative")->withAnyArgs();

		$mockTentativeDAO->shouldReceive("save")->andReturnArg(2);

		$this->assertEquals(200, $résultat_obtenu->status());
		$this->assertLessThan(
			1,
			$heure_courante - $heure_tentative,
			"Heure courante: {$heure_courante}, Heure tentative: {$heure_tentative}",
		);

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
		$this->assertEquals('{"erreur":"Tentative intraitable."}', $résultat_obtenu->getContent());
	}

	public function test_étant_donné_le_username_dun_utilisateur_le_chemin_dune_question_et_le_timestamp_lorsquon_appelle_post_avec_un_test_unique_en_parametre_lavancement_et_la_tentative_ne_sont_pas_sauvegardés_et_obtient_la_TentativeProg_avec_ses_résultats_et_ses_relations_sous_forme_json()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/tentatives?include=resultats",
			[
				"langage" => "python",
				"code" => "#+TODO\nprint(\"Hello world!\")",
				"test" => ["nom" => "Test bonjour", "sortie_attendue" => "bonjour", "entrée" => "bonjour"],
			],
		);
		$mockTentativeDAO = Mockery::mock("progression\\dao\\tentative\\TentativeProgDAO");
		$mockTentativeDAO->shouldNotReceive("save")->withAnyArgs();

		$mockAvancementDAO = Mockery::mock("progression\\dao\\AvancementDAO");
		$mockAvancementDAO->shouldNotReceive("get_avancement")->withAnyArgs();
		$mockAvancementDAO->shouldNotReceive("save")->withAnyArgs();
		$this->assertEquals(200, $résultat_obtenu->status());

		$heure_courante = time();
		$heure_tentative = json_decode($résultat_obtenu->getContent())->data->attributes->date_soumission;
		$this->assertLessThan(
			1,
			$heure_courante - $heure_tentative,
			"Heure courante: {$heure_courante}, Heure tentative: {$heure_tentative}",
		);

		$this->assertJsonStringEqualsJsonString(
			sprintf(file_get_contents(__DIR__ . "/résultats_attendus/tentativeCtlTest_3.json"), $heure_tentative),
			$résultat_obtenu->getContent(),
		);
	}

	public function test_étant_donné_une_tentative_ayant_du_code_dépassant_la_taille_maximale_de_caractères_on_obtient_une_erreur_413()
	{
		$_ENV["TAILLE_CODE_MAX"] = 23;
		$testCode = "#+TODO\n日本語でのテストです\n#-TODO"; //24 caractères UTF8

		$résultat_obtenu = $this->actingAs($this->user)->call("POST", "/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/tentatives", [
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

		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/tentatives",
			["langage" => "python", "code" => "$testCode"],
		);

		$this->assertEquals(200, $résultat_obtenu->status());
	}

}
