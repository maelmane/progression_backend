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

namespace progression\dao;

use progression\dao\tentative\TentativeDAO;
use progression\domaine\entité\{Avancement, Question, TentativeProg, Sauvegarde};
use PHPUnit\Framework\TestCase;
use Mockery;

final class AvancementDAOTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();
		EntitéDAO::get_connexion()->begin_transaction();

		// Tentative
		$mockTentativeDao = Mockery::mock("progression\\dao\\tentative\\TentativeDAO");
		$mockTentativeDao
			->allows()
			->get_toutes("Bob", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn([new TentativeProg("python", 'print("Tourlou le monde!")', 1615696276)]);
		$mockTentativeDao->shouldReceive("get_toutes")->andReturn([]);

		// Sauvegarde
		$mockSauvegardeDao = Mockery::mock("progression\\dao\\SauvegardeDAO");
		$mockSauvegardeDao
			->allows()
			->get_toutes("Bob", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn([new Sauvegarde(1620150294, "print(\"Hello world!\")")]);
		$mockSauvegardeDao->shouldReceive("get_toutes")->andReturn([]);

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_tentative_dao()
			->andReturn($mockTentativeDao);
		$mockDAOFactory
			->allows()
			->get_sauvegarde_dao()
			->andReturn($mockSauvegardeDao);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		parent::tearDown();

		EntitéDAO::get_connexion()->rollback();
		Mockery::close();
		DAOFactory::setInstance(null);
	}

	public function test_étant_donné_un_avancement_existant_lorsquon_cherche_par_username_et_question_uri_on_obtient_un_objet_avancement_correspondant()
	{
		$résultat_attendu = new Avancement(
			etat: Question::ETAT_DEBUT,
			type: Question::TYPE_PROG,
			tentatives: [new TentativeProg("python", 'print("Tourlou le monde!")', 1615696276)],
			titre: "Un titre",
			niveau: "facile",
			date_modification: 1645739981,
			date_réussite: 1645739959,
			sauvegardes: [new Sauvegarde(1620150294, "print(\"Hello world!\")")],
		);

		$résponse_observée = (new AvancementDAO())->get_avancement(
			"Bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
		);
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

	public function test_étant_donné_un_avancement_inexistant_lorsquon_le_cherche_par_username_et_question_uri_on_obtient_null()
	{
		$résponse_observée = (new AvancementDAO())->get_avancement(
			"Bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction_inexistante",
		);
		$this->assertNull($résponse_observée);
	}

	public function test_étant_donné_un_nouvel_avancement_lorsquon_le_sauvegarde_il_est_sauvegardé_dans_la_BD_et_on_obtient_lavancement()
	{
		$nouvel_avancement = new Avancement(
			etat: Question::ETAT_DEBUT,
			type: Question::TYPE_PROG,
			tentatives: [],
			titre: "Un titre",
			niveau: "facile",
			date_modification: 1645739981,
			date_réussite: 1645739959,
		);

		// L'avancement est retourné
		$résponse_observée = (new AvancementDAO())->save(
			"Bob",
			"https://depot.com/roger/une_nouvelle_question",
			$nouvel_avancement,
		);

		$this->assertEquals($nouvel_avancement, $résponse_observée);

		// L'avancement a été sauvegardé
		$résponse_observée = (new AvancementDAO())->get_avancement(
			"Bob",
			"https://depot.com/roger/une_nouvelle_question",
		);

		$this->assertEquals($nouvel_avancement, $résponse_observée);
	}

	public function test_étant_donné_un_avancement_existant_lorsquon_le_modifie_il_est_sauvegardé_dans_la_BD_et_on_obtient_lavancement_modifié()
	{
		$avancement = (new AvancementDAO())->get_avancement(
			"Bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
		);

		$avancement->titre = "Nouveau titre";
		$avancement->niveau = "Nouveau niveau";
		$avancement->etat = Question::ETAT_REUSSI;
		$avancement->date_modification = 1645740000;
		$avancement->date_réussite = 1645740000;

		$résultat_attendu = new Avancement(
			etat: Question::ETAT_REUSSI,
			type: Question::TYPE_PROG,
			tentatives: [new TentativeProg("python", 'print("Tourlou le monde!")', 1615696276)],
			titre: "Nouveau titre",
			niveau: "Nouveau niveau",
			date_modification: 1645740000,
			date_réussite: 1645740000,
			sauvegardes: [new Sauvegarde(1620150294, "print(\"Hello world!\")")],
		);

		// L'avancement est retourné
		$résponse_observée = (new AvancementDAO())->save(
			"Bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			$avancement,
		);

		$this->assertEquals($résultat_attendu, $résponse_observée);

		// L'avancement a été sauvegardé
		$résponse_observée = (new AvancementDAO())->get_avancement(
			"Bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
		);

		$this->assertEquals($résponse_observée, $résponse_observée);
	}
}
