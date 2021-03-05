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

namespace progression\domaine\interacteur;

use progression\domaine\entité\TentativeProg;
use PHPUnit\Framework\TestCase;
use \Mockery;

final class ObtenirTentativeIntTests extends TestCase
{
	public function test_étant_donné_une_tentative_avec_des_attributs_lorsque_cherché_par_user_id_question_id_et_date_soumission_on_obtient_un_objet_tentative_correspondant()
	{
		$résultat_attendu = new TentativeProg("java", "System.out.println();", 1614711760);

		$mockAvancementProgDAO = Mockery::mock("progression\dao\AvancementProgDAO");
		$mockAvancementProgDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "prog1/les_fonctions_01/appeler_une_fonction_paramétrée", 1614711760)
			->andReturn($résultat_attendu);

		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_avancement_prog_dao()
			->andReturn($mockAvancementProgDAO);

		$interacteur = new ObtenirTentativeInt($mockDAOFactory);
		$résultat_obtenu = $interacteur->get_tentative(
			"jdoe",
			"prog1/les_fonctions_01/appeler_une_fonction_paramétrée",
			1614711760
		);

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}
}
