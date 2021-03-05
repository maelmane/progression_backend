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

final class TentativeProgTests extends TestCase
{
	public function test_étant_donné_une_TentativeProg_instanciée_avec_langage_python_lorsquon_récupère_son_langage_on_obtient_python()
	{
		$TentativeProgTest = new TentativeProg("python", 5, 8);

		$langage = $TentativeProgTest->langage;

		$this->assertEquals("python", $langage);
	}

	public function test_étant_donné_une_TentativeProg_instanciée_avec_code_5_lorsquon_récupère_son_code_on_obtient_5()
	{
		$TentativeProgTest = new TentativeProg("python", 5, 8);

		$code = $TentativeProgTest->code;

		$this->assertEquals(5, $code);
	}

	public function test_étant_donné_une_TentativeProg_instanciée_avec_date_soumission_8_lorsquon_récupère_sa_date_soumission_on_obtient_8()
	{
		$TentativeProgTest = new TentativeProg("python", 5, 8);

		$date_soumission = $TentativeProgTest->date_soumission;

		$this->assertEquals(8, $date_soumission);
	}
}
