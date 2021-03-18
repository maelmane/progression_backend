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

use progression\domaine\entité\{Exécutable, Test, RésultatProg};
use PHPUnit\Framework\TestCase;
use \Mockery;

final class ExécuterProgIntTests extends TestCase
{
	public function test_étant_donné_un_exécutable_python_correct_et_un_test_lorsquon_les_soumet_pour_exécution_on_obtient_un_résultat_de_test_avec_ses_sorties_observées()
	{
		$_ENV['COMPILEBOX_URL'] = "file://" . __DIR__ . "/résultats_exécution/test_exec_prog_int_1";
		$_SERVER["REMOTE_ADDR"] = "";
		$_SERVER["PHP_SELF"] = "";

		$exécutable = new Exécutable("a=int(input())\nfor i in range(a):print('ok')", "python");
		$test = new Test("premier test", "1", "ok\n");

		$résultat_attendu = new RésultatProg("ok\n", "");

        $résultat_observé = (new ExécuterProgInt(null))->exécuter($exécutable, $test);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}
}
