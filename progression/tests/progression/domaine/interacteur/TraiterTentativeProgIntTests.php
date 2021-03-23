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

use progression\domaine\entité\{Test, RésultatProg, TentativeProg};
use PHPUnit\Framework\TestCase;

final class TraiterTentativeProgIntTests extends TestCase
{
	public function test_étant_donné_des_résultats_valides_et_des_tests_dune_question_lorsquon_les_traites_on_obtient_des_résultats()
	{
		$résultats = [
			new RésultatProg("ok\n", ""),
			new RésultatProg("ok\nok\nok\nok\nok\nok\n", "")
		];
		$tests = [
			new Test("premier test", "1", "ok\n"),
			new Test("premier test", "5", "ok\nok\nok\nok\nok\n"),
		];
		$résultat_attendu = [
			"tests_réussis" => 2,
			"résultat_prog" => [
				new RésultatProg("ok\n", "", true),
				new RésultatProg("ok\nok\nok\nok\nok\n", "", true)
			]
		];
		$résultat_observé = (new TraiterTentativeProgInt(null))->traiter_résultats($résultats, $tests);
	}

	public function test_étant_donné_des_résultats_nonvalides_et_des_tests_dune_question_lorsquon_les_traites_on_obtient_des_résultats()
	{
		$résultats = [
			new RésultatProg("ok\n", ""),
			new RésultatProg("ok\nok", ""),
			new RésultatProg("ok\nok\nok", "")
		];
		$tests = [
			new Test("premier test", "1", "ok\n"),
			new Test("deuxième test", "5", "ok\nok\nok\nok\nok\n"),
			new Test("troisième test", "10", "ok\nok\nok\nok\nok\nok\nok\nok\nok\nok\n")
		];
		$résultat_attendu = [
			"tests_réussis" => 1,
			"résultat_prog" => [
				new RésultatProg("ok\n", "", true),
				new RésultatProg("ok\nok", "", false),
				new RésultatProg("ok\nok\nok", "", false)
			]
		];
		$résultat_observé = (new TraiterTentativeProgInt(null))->traiter_résultats($résultats, $tests);
	}
}
