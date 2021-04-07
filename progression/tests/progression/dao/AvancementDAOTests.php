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

require_once __DIR__ . "/../../TestCase.php";

use progression\domaine\entité\{Avancement, Question, TentativeProg};
use Mockery;

final class AvancementDAOTests extends \TestCase
{
	public function setUp(): void
	{
		EntitéDAO::get_connexion()->begin_transaction();
	}

	public function tearDown(): void
	{
		EntitéDAO::get_connexion()->rollback();
		Mockery::close();
	}

	public function test_étant_donné_un_avancement_existant_lorsquon_cherche_par_username_et_question_uri_on_obtient_un_objet_avancement_correspondant()
	{
		$résultat_attendu = new Avancement([
			new TentativeProg("python", 'print("Tourlou le monde!")', 1615696276)
		]);
		$résultat_attendu->type = Question::TYPE_PROG;

		$mockTentativeProgDao = Mockery::mock('progression\dao\TentativeProgDAO');
		$mockTentativeProgDao
			->allows()
			->get_toutes("bob", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn([
				new TentativeProg("python", 'print("Tourlou le monde!")', 1615696276)
			]);

		$mockDAOFactory = Mockery::mock('progression\dao\DAOFactory');
		$mockDAOFactory
			->allows()
			->get_tentative_prog_dao()
			->andReturn($mockTentativeProgDao);

		$résponse_observée = (new AvancementDAO($mockDAOFactory))->get_avancement("bob", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction");
		$this->assertEquals($résultat_attendu, $résponse_observée);
	}

	public function test_étant_donné_un_avancement_inexistant_lorsquon_le_cherche_par_username_et_question_uri_on_obtient_null()
	{
		$réponse_attendue = null;

		$résponse_observée = (new AvancementDAO())->get_avancement("bobert", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction_inexistante");
		$this->assertEquals($réponse_attendue, $résponse_observée);
	}
}
