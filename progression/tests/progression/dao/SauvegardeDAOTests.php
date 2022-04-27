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
use progression\TestCase;
use Mockery;

final class SauvegardeDAOTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();
		EntitéDAO::get_connexion()->begin_transaction();
	}

	public function tearDown(): void
	{
		EntitéDAO::get_connexion()->rollback();
		Mockery::close();
	}

	public function test_étant_donné_une_sauvegarde_existante_lorsquon_cherche_par_username_question_uri_et_langage_on_obtient_un_objet_sauvegarde_correspondant()
	{
		$résultat_attendu = new Sauvegarde(1620150294, "print(\"Hello world!\")");

		$résponse_observée = (new SauvegardeDAO())->get_sauvegarde(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			"python",
		);
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

	public function test_étant_donné_un_username_inexistant_lorsquon_cherche_une_sauvegarde_on_obtient_un_objet_null()
	{
		$résponse_observée = (new SauvegardeDAO())->get_sauvegarde(
			"Marcel",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			"python",
		);
		$this->assertNull($résponse_observée);
	}

	public function test_étant_donné_une_question_uri_inexistante_lorsquon_cherche_une_sauvegarde_on_obtient_un_objet_null()
	{
		$résponse_observée = (new SauvegardeDAO())->get_sauvegarde(
			"bob",
			"https://depot.com/roger/questions_prog/question_inexistante",
			"python",
		);
		$this->assertNull($résponse_observée);
	}

	public function test_étant_donné_un_langage_inexistant_lorsquon_cherche_une_sauvegarde_on_obtient_un_objet_null()
	{
		$résponse_observée = (new SauvegardeDAO())->get_sauvegarde(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			"c#",
		);
		$this->assertNull($résponse_observée);
	}

	public function test_étant_donné_une_sauvegarde_instanciée_lorsquon_lenregistre_on_obtient_un_objet_sauvegarde_correspondant()
	{
		$résultat_attendu = new Sauvegarde(1620150294, "print(\"Hello world!\")");

		$résponse_observée1 = (new SauvegardeDAO())->save(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			"python",
			new Sauvegarde(1620150294, "print(\"Hello world!\")"),
		);

		$résponse_observée2 = (new SauvegardeDAO())->get_sauvegarde(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
			"python",
		);

		$this->assertEquals($résultat_attendu, $résponse_observée1);
		$this->assertEquals($résultat_attendu, $résponse_observée2);
	}

	public function test_étant_donné_une_liste_de_sauvegardes_existante_lorsquon_cherche_par_username_et_question_uri_on_obtient_un_tableau_de_sauvegardes_correspondant()
	{
		$résultat_attendu = [];
		$résultat_attendu["python"] = new Sauvegarde(1620150294, "print(\"Hello world!\")");
		$résultat_attendu["java"] = new Sauvegarde(1620150375, "System.out.println(\"Hello world!\");");

		$résponse_observée = (new SauvegardeDAO())->get_toutes(
			"bob",
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
		);
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}
}
