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

use progression\domaine\entité\Sauvegarde;
use PHPUnit\Framework\TestCase;
use Mockery;

final class SauvegardeDAOTests extends TestCase
{
	public function setUp(): void
	{
		EntitéDAO::get_connexion()->begin_transaction();

		$sauvegarde = new Sauvegarde
        (
            "jdoe",
            "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
            1620150294,
            "python",
            "print(\"Hello world!\")"
        );
		$mockSauvegardeDAO = Mockery::mock("progression\dao\SauvegardeDAO");
		$mockSauvegardeDAO
			->shouldReceive("get_sauvegarde")
			->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", "python")
			->andReturn($sauvegarde);
		$mockSauvegardeDAO
			->shouldReceive("get_sauvegarde")
			->with("Marcel", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", "python")
			->andReturn(null);
        $mockSauvegardeDAO
			->shouldReceive("get_sauvegarde")
			->with("jdoe", "https://depot.com/roger/questions_prog/question_inexistante", "python")
			->andReturn(null);
        $mockSauvegardeDAO
			->shouldReceive("get_sauvegarde")
			->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", "java")
			->andReturn(null);
		$mockSauvegardeDAO
			->shouldReceive("save")
			->andReturn($sauvegarde);

		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_sauvegarde_dao()
			->andReturn($mockSauvegardeDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		EntitéDAO::get_connexion()->rollback();
		Mockery::close();
	}

	public function test_étant_donné_une_sauvegarde_existante_lorsquon_cherche_par_username_question_uri_et_langage_on_obtient_un_objet_sauvegarde_correspondant()
	{
		$résultat_attendu = new Sauvegarde
        (
            "jdoe",
            "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
            1620150294,
            "python",
            "print(\"Hello world!\")"
        );

		$résponse_observée = (new SauvegardeDAO())->get_sauvegarde("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", "python");
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

	public function test_étant_donné_un_username_inexistant_lorsquon_cherche_une_sauvegarde_on_obtient_un_objet_null()
	{
		$résultat_attendu = null;

		$résponse_observée = (new SauvegardeDAO())->get_sauvegarde(
			"Marcel",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
            "python"
		);
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

    public function test_étant_donné_une_question_uri_inexistante_lorsquon_cherche_une_sauvegarde_on_obtient_un_objet_null()
	{
		$résultat_attendu = null;

		$résponse_observée = (new SauvegardeDAO())->get_sauvegarde(
			"jdoe",
			"https://depot.com/roger/questions_prog/question_inexistante",
            "python"
		);
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

    public function test_étant_donné_un_langage_inexistant_lorsquon_cherche_une_sauvegarde_on_obtient_un_objet_null()
	{
		$résultat_attendu = null;

		$résponse_observée = (new SauvegardeDAO())->get_sauvegarde(
			"jdoe",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
            "java"
		);
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

    public function test_étant_donné_une_sauvegarde_instanciée_lorsquon_lenregistre_on_obtient_un_objet_sauvegarde_correspondant()
	{
		$résultat_attendu = new Sauvegarde
        (
            "jdoe",
            "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
            1620150294,
            "python",
            "print(\"Hello world!\")"
        );

		$résponse_observée = (new SauvegardeDAO())->save(
		    new Sauvegarde
                (
                    "jdoe",
                    "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
                    1620150294,
                    "python",
                    "print(\"Hello world!\")"
                )
		);
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}
}
