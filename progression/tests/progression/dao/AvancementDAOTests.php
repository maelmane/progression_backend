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
			->get_toutes("bob", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn([new TentativeProg("python", 'print("Tourlou le monde!")', 1615696276)]);
		$mockTentativeDao
			->allows()
			->get_toutes(
				"bobert",
				"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction_inexistante",
			)
			->andReturn([]);

		// Sauvegarde
		$mockSauvegardeDao = Mockery::mock("progression\\dao\\SauvegardeDAO");
		$mockSauvegardeDao
			->allows()
			->get_toutes("bob", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn([new Sauvegarde(1620150294, "print(\"Hello world!\")")]);
		$mockSauvegardeDao
			->allows()
			->get_toutes(
				"bobert",
				"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction_inexistante",
			)
			->andReturn([]);

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

	public function test_étant_donné_un_avancement_existant_lorsquon_cherche_par_username_et_question_uri_incluant_les_tentatives_on_obtient_un_objet_avancement_correspondant_avec_ses_tentatives()
	{
		$résultat_attendu = new Avancement(
			0,
			0,
			[new TentativeProg("python", 'print("Tourlou le monde!")', 1615696276)],
			[],
		);
		$résultat_attendu->type = Question::TYPE_PROG;
		$résultat_attendu->titre = "Bob";
		$résultat_attendu->niveau = "facile";
		$résultat_attendu->date_modification = 1645739981;
		$résultat_attendu->date_réussite = 1645739959;

		$résponse_observée = (new AvancementDAO())->get_avancement(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			["tentatives"]
		);
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

	public function test_étant_donné_un_avancement_existant_lorsquon_cherche_par_username_et_question_uri_incluant_les_sauvegardes_on_obtient_un_objet_avancement_correspondant_avec_ses_sauvegardes()
	{
		$résultat_attendu = new Avancement(
			0,
			0,
			[],
			[new Sauvegarde(1620150294, "print(\"Hello world!\")")],
		);
		$résultat_attendu->type = Question::TYPE_PROG;
		$résultat_attendu->titre = "Bob";
		$résultat_attendu->niveau = "facile";
		$résultat_attendu->date_modification = 1645739981;
		$résultat_attendu->date_réussite = 1645739959;

		$résponse_observée = (new AvancementDAO())->get_avancement(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			["sauvegardes"]
		);
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

	public function test_étant_donné_un_avancement_existant_lorsquon_cherche_par_username_et_question_uri_incluant_les_tentatives_et_sauvegardes_on_obtient_un_objet_avancement_correspondant_avec_ses_tentatives_et_sauvegardes()
	{
		$résultat_attendu = new Avancement(
			0,
			0,
			[new TentativeProg("python", 'print("Tourlou le monde!")', 1615696276)],
			[new Sauvegarde(1620150294, "print(\"Hello world!\")")],
		);
		$résultat_attendu->type = Question::TYPE_PROG;
		$résultat_attendu->titre = "Bob";
		$résultat_attendu->niveau = "facile";
		$résultat_attendu->date_modification = 1645739981;
		$résultat_attendu->date_réussite = 1645739959;

		$résponse_observée = (new AvancementDAO())->get_avancement(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			["tentatives", "sauvegardes"]
		);
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

	public function test_étant_donné_un_avancement_inexistant_lorsquon_le_cherche_par_username_et_question_uri_on_obtient_null()
	{
		$résponse_observée = (new AvancementDAO())->get_avancement(
			"bobert",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction_inexistante",
		);
		$this->assertNull($résponse_observée);
	}
}
