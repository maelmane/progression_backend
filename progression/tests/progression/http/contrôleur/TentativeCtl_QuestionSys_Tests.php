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
	TestSys,
	Exécutable,
	Question,
	QuestionSys,
	TentativeSys,
	User,
};

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
		$question_solution_courte_réussie= new QuestionSys(
			type: Question::TYPE_SYS,
			nom: "toutes_les_permissions",
			solution: "~laSolution~",
			uri: "https://depot.com/question_solution_courte_réussie",
			feedback_pos: "Bon travail!",
			feedback_neg: "Encore un effort!");

		$question_solution_courte_non_réussie= new QuestionSys(
			type: Question::TYPE_SYS,
			nom: "toutes_les_permissions",
			solution: "~laSolution~",
			uri: "https://depot.com/question_solution_courte_non_réussie",
			feedback_pos: "Bon travail!",
			feedback_neg: "Encore un effort!");
		
		//QuestionSys avec validations
		$question_validée_réussie = new QuestionSys(
			type: Question::TYPE_SYS,
			nom: "toutes_les_permissions_2",
			uri: "https://depot.com/roger/question_validée_réussie",
			feedback_pos: "Bon travail!",
			feedback_neg: "Encore un effort!",
			tests:  [
				new TestSys(
					nom: "Toutes permissions 3",
					sortie_attendue: "-rwxrwxrwx",
					validation: "laValidation",
					utilisateur: "momo",
					feedback_pos: "yes!",
					feedback_neg: "non!",
				),
		]);

		$question_validée_non_réussie = new QuestionSys(
			type: Question::TYPE_SYS,
			nom: "toutes_les_permissions_2",
			uri: "https://depot.com/roger/question_validée_non_réussie",
			feedback_pos: "Bon travail!",
			feedback_neg: "Encore un effort!",
			tests:  [
				new TestSys(
					nom: "Toutes permissions 3",
					sortie_attendue: "-rwxrwxrwx",
					validation: "laValidation",
					utilisateur: "momo",
					feedback_pos: "yes!",
					feedback_neg: "non!",
				),
		]);

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

		// Tentatives
		$this->tentative_solution_courte_non_réussie = new TentativeSys(
			conteneur: ["id" => "leConteneurDeLancienneTentative",
						"ip" => "192.168.0.1",
						"port" => 12345 ],
			réponse: "laRéponseDeLancienneTentative",
			date_soumission: "1614374490",
			réussi: false,
		);
		$this->tentative_solution_courte_réussie = new TentativeSys(
			conteneur: ["id" => "leConteneurDeLancienneTentative2",
						"ip" => "192.168.0.1",
						"port" => 12345 ],
			réponse: "laRéponseDeLancienneTentative2",
			date_soumission: "1614374491",
			réussi: true,
		);
		$this->tentative_validée_non_réussie = new TentativeSys(
			conteneur: ["id" => "leConteneurDeLancienneTentative",
						"ip" => "192.168.0.1",
						"port" => 12345 ],
			réponse: null,
			date_soumission: "1614374490",
			réussi: false,
		);
		$this->tentative_validée_réussie = new TentativeSys(
			conteneur: ["id" => "leConteneurDeLancienneTentative2",
						"ip" => "192.168.0.1",
						"port" => 12345 ],
			réponse: null,
			date_soumission: "1614374491",
			réussi: true,
		);
		
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "https://depot.com/question_solution_courte_non_réussie")
			->andReturn($this->tentative_solution_courte_non_réussie);
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "https://depot.com/question_solution_courte_réussie")
			->andReturn($this->tentative_solution_courte_réussie);
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "https://depot.com/question_validée_non_réussie")
			->andReturn($this->tentative_validée_non_réussie);
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "https://depot.com/question_validée_réussie")
			->andReturn($this->tentative_validée_réussie);

		$mockTentativeDAO
			->shouldReceive("get_toutes")
			->with("jdoe", "https://depot.com/question_validée_réussie")
			->andReturn([
				$this->tentative_validée_non_réussie,
				$this->tentative_validée_réussie
			]);

		$mockTentativeDAO
			->shouldReceive("get_dernière")
			->with("jdoe", "https://depot.com/question_validée_réussie")
			->andReturn( $this->tentative_validée_réussie );

		// Exécuteur
		$mockExécuteur
		   ->shouldReceive("exécuter_sys")
		   ->withArgs(function ($question, $tentative) {
			   return $question == $question_validée;
		   })
		   ->andReturn([
			   "temps_exec" => 0.5,
			   "résultats" => [["output" => "Incorrecte", "time" => 0.1]],
			   "conteneur" => [
				   "id" => "leConteneurDeLaNouvelleTentative",
				   "ip" => "172.45.2.2",
				   "port" => 45667
			   ],
		   ]);

		//Avancement
		$this->avancement_solution_courte_non_réussie = new Avancement(Question::ETAT_NONREUSSI, Question::TYPE_SYS, [
			$this->tentative_solution_courte_non_réussie
		]);
		$this->avancement_solution_courte_réussie = new Avancement(Question::ETAT_REUSSI, Question::TYPE_SYS, [
			$this->tentative_solution_courte_non_réussie,
			$this->tentative_solution_courte_réussie
		]);
		$this->avancement_validée_non_réussie = new Avancement(Question::ETAT_NONREUSSI, Question::TYPE_SYS, [
			$this->tentative_validée_non_réussie
		]);
		$this->avancement_validée_réussie = new Avancement(Question::ETAT_REUSSI, Question::TYPE_SYS, [
			$this->tentative_validée_non_réussie,
			$this->tentative_validée_réussie
		]);

		$mockAvancementDAO
		   ->shouldReceive("get_avancement")
		   ->with("jdoe", "https://depot.com/question_validée_non_réussie")
		   ->andReturn($this->avancement_validée_non_réussie);
		$mockAvancementDAO
		   ->shouldReceive("get_avancement")
		   ->with("jdoe", "https://depot.com/question_validée_réussie")
		   ->andReturn($this->avancement_validée_réussie);
		$mockAvancementDAO
		   ->shouldReceive("get_avancement")
		   ->with("jdoe", "https://depot.com/question_solution_courte_non_réussie")
		   ->andReturn($this->avancement_solution_courte_non_réussie);
		$mockAvancementDAO
		   ->shouldReceive("get_avancement")
		   ->with("jdoe", "https://depot.com/question_solution_courte_réussie")
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
		$mockDAOFactory->shouldReceive("get_tentative_sys_dao")->andReturn($mockTentativeDAO);
		$mockDAOFactory->shouldReceive("get_exécuteur")->andReturn($mockExécuteur);
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		
	}

	public function test_étant_donné_un_avancement_non_réussi_pour_une_QuestionSys_à_solution_courte_lorsquon_soumet_la_bonne_réponse_lavancement_et_la_tentative_sont_sauvegardés_et_on_obtient_la_TentativeSys_réussie()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcXVlc3Rpb25fc29sdXRpb25fY291cnRl/tentatives?include=resultats",
			["réponse" => "laSolution"],
		);

		$heure_courante = time();
		$heure_tentative = json_decode($résultat_obtenu->getContent())->data->attributes->date_soumission;

		$nouvelle_tentative = new TentativeProg(
			conteneur: [
				"id" => "leConteneurDeLancienneTentative",
				"ip" => "192.168.0.1",
				"port" => 12345
			],
			réponse: "laSolution",
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

		$mockTentativeDAO = DAOFactory::getInstance()->get_tentative_dao();
		$mockTentativeDAO->shouldReceive("save")
						  ->withArgs(function ($user, $uri, $t) use ($nouvelle_tentative) {
							  return $user == "jdoe" &&
									 $uri == "https://depot.com/questions_solution_courte_non_réussie" &&
									 $t == $nouvelle_tentative;
						  })
						  ->andReturnArg($nouvelle_tentative);

		$mockAvancementDAO = DAOFactory::getInstance()->get_avancement_dao();
		$mockAvancementDAO->shouldReceive("save")
						  ->withArgs(function ($user, $uri, $av) use ($nouvel_avancement) {
							  return $user == "jdoe" &&
									 $uri == "https://depot.com/questions_solution_courte_non_réussie" &&
									 $av == $nouvel_avancement;
						  })
						  ->andReturnArg($nouvel_avancement);

		$heure_tentative = json_decode($résultat_obtenu->getContent())->data->attributes->date_soumission;

		$this->assertEquals(200, $résultat_obtenu->status());

		$this->assertJsonStringEqualsJsonString(
			sprintf(file_get_contents(__DIR__ . "/résultats_attendus/tentativeCtlTest_5.json"), $heure_tentative),
			$résultat_obtenu->getContent(),
		);
	}
	/*
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

	   public function test_étant_donné_une_question_sys_avec_tests_de_validation_lorsquon_soumet_une_tentative_validée_réussie_lavancement_et_la_tentative_sont_sauvegardée_et_on_obtient_une_TentativeSys_réussie(){
	   }
	   
	   public function test_étant_donné_une_question_sys_avec_tests_de_validation_lorsquon_soumet_une_tentative_échouée_lavancement_et_la_tentative_sont_sauvegardée_et_on_obtient_une_TentativeSys_échouée(){
	   }
	 */


}
