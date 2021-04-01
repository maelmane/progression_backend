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

use progression\domaine\entité\{QuestionProg, TentativeProg, Test, RésultatProg};
use PHPUnit\Framework\TestCase;

final class TraiterTentativeProgIntTests extends TestCase
{
	public function test_étant_donné_une_TentativeProg_valides_et_une_QuestionProg_lorsquon_les_traites_on_obtient_une_TentativeProg_traitée_et_réussie()
	{
		$question = new QuestionProg();
		$question->tests = [
			new Test("premier test", "1", "ok\n"),
			new Test("deuxième test", "5", "ok\nok\nok\nok\nok\n"),
		];
		$question->feedback_pos = "Bravo!";
		$question->feedback_neg = "As-tu essayé de ne pas faire ça?";

		$tentative = new TentativeProg("python", "testCode");
		$tentative->résultats = [
			new RésultatProg("ok\n", ""),
			new RésultatProg("ok\nok\nok\nok\nok\n", "")
		];

		$résultat_observé = (new TraiterTentativeProgInt(null))->traiter_résultats($question, $tentative);

		$this->assertEquals(2, $résultat_observé->tests_réussis);
		$this->assertTrue($résultat_observé->réussi);
		$this->assertEquals("Bravo!", $résultat_observé->feedback);
	}

	public function test_étant_donné_une_TentativeProg_nonvalides_et_une_QuestionProg_lorsquon_les_traites_on_obtient_une_TentativeProg_traitée_et_nonréussie()
	{
		$question = new QuestionProg();
		$question->tests = [
			new Test("premier test", "1", "ok\n"),
			new Test("deuxième test", "5", "ok\nok\nok\nok\nok\n"),
			new Test("troisième test", "10", "ok\nok\nok\nok\nok\nok\nok\nok\nok\nok\n")
		];
		$question->feedback_pos = "Bravo!";
		$question->feedback_neg = "As-tu essayé de ne pas faire ça?";

		$tentative = new TentativeProg("python", "testCode");
		$tentative->résultats = [
			new RésultatProg("ok\n", ""),
			new RésultatProg("ok\nok\nok\n", ""),
			new RésultatProg("ok\nok\nok\nok\nok\n", "")
		];

		$résultat_observé = (new TraiterTentativeProgInt(null))->traiter_résultats($question, $tentative);

		$this->assertEquals(1, $résultat_observé->tests_réussis);
		$this->assertFalse($résultat_observé->réussi);
	}

	public function test_étant_donné_une_TentativeProg_avec_une_erreur_et_une_QuestionProg_lorsquon_les_traites_on_obtient_une_TentativeProg_traitée_et_nonréussie()
	{
		$question = new QuestionProg();
		$question->tests = [
			new Test("premier test", "1", "ok\n"),
			new Test("deuxième test", "5", "ok\nok\nok\nok\nok\n"),
		];
		$question->feedback_pos = "Bravo!";
		$question->feedback_neg = "As-tu essayé de ne pas faire ça?";

		$tentative = new TentativeProg("python", "testCode");
		$tentative->résultats = [
			new RésultatProg("ok\n", ""),
			new RésultatProg("", "testErreur")
		];

		$résultat_observé = (new TraiterTentativeProgInt(null))->traiter_résultats($question, $tentative);

		$this->assertEquals(1, $résultat_observé->tests_réussis);
		$this->assertFalse($résultat_observé->réussi);
		$this->assertEquals("As-tu essayé de ne pas faire ça?", $résultat_observé->feedback);
	}
}
