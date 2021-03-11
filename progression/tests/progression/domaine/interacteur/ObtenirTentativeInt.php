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
	public function test_étant_donné_une_tentative_avec_un_username_question_id_et_date_existant_lorsque_cherché_par_username_question_id_et_date_on_obtient_un_objet_tentativeprog_correspondant()
	{
		$username = "bob";
		$question_uri =
			"https://progression.pages.dti.crosemont.quebec/progression_contenu_demo/les_fonctions_01/appeler_une_fonction_avec_retour";
		$date = 1614965817;

		$résultat_attendu = new TentativeProg("python", "print()", $date);

		$mockAvancementDAO = Mockery::mock("progression\dao\AvancementProgDAO");
		$mockAvancementDAO
			->shouldReceive("get_tentative")
			->with()
			->andReturn($résultat_attendu);

		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_avancement_prog_dao()
			->andReturn($mockAvancementDAO);

		$interacteur = new ObtenirTentativeInt($mockDAOFactory);
		$résultat_obtenu = $interacteur->get_tentative(
			$username,
			$question_uri,
			$date
		);

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}
}
