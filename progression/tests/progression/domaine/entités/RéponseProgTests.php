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

namespace progression\domaine\entité;

use PHPUnit\Framework\TestCase;

final class RéponseProgTests extends TestCase
{
	public function test_étant_donné_une_réponseProg_instanciée_avec_langid_2_lorsquon_récupère_son_langid_on_obtient_2()
	{
		$réponseProgTest = new RéponseProg(2, 5, 8);

		$langid = $réponseProgTest->langid;

		$this->assertEquals(2, $langid);
	}

	public function test_étant_donné_une_réponseProg_instanciée_avec_code_5_lorsquon_récupère_son_code_on_obtient_5()
	{
		$réponseProgTest = new RéponseProg(2, 5, 8);

		$code = $réponseProgTest->code;

		$this->assertEquals(5, $code);
	}

	public function test_étant_donné_une_réponseProg_instanciée_avec_date_soumission_8_lorsquon_récupère_sa_date_soumission_on_obtient_8()
	{
		$réponseProgTest = new RéponseProg(2, 5, 8);

		$date_soumission = $réponseProgTest->date_soumission;

		$this->assertEquals(8, $date_soumission);
	}
}
