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

use progression\domaine\entité\{Avancement};
use PHPUnit\Framework\TestCase;
use \Mockery;

final class SauvegarderAvancementProgIntTests extends TestCase
{
	public function test()
	{
		$avancement = new Avancement();
		$username = "Bob";
		$question_uri = "https://example.com/question";

		$mockAvancementDao = Mockery::mock("progression\dao\AvancementDAO");
		$mockAvancementDao
			->allows()
			->save("https://example.com/question", "Bob", $avancement);

		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_avancement_dao()
			->andReturn($mockAvancementDao);

		$résultat_attendu = "";

		$interacteur = new SauvegarderAvancementInt($mockDAOFactory);
		$résultat_observé = $interacteur->sauvegarder("https://example.com/question", $avancement, "Bob");

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}
}
