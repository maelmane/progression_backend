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

use progression\domaine\entité\{Avancement, Question, TentativeProg};
use progression\dao\exécuteur\ExécutionException;

class SoumettreTentativeProgInt extends Interacteur
{
	public function soumettre_tentative($question, $tests, $tentative): TentativeProg|null
	{
		$exécutable = null;

		$préparerProgInt = new PréparerProgInt();
		$exécutable = $préparerProgInt->préparer_exécutable($question, $tentative);

		if (!$exécutable) {
			return null;
		}

		try {
			$résultats = $this->exécuter_prog($exécutable, $tests);
		} catch (ExécutionException $e) {
			throw new IntéracteurException($e, 503);
		}
		if ($résultats) {
			$tentative->temps_exécution = $résultats["temps_exécution"];
			$tentative->résultats = $résultats["résultats"];
			$rétroactions["feedback_pos"] = $question->feedback_pos;
			$rétroactions["feedback_neg"] = $question->feedback_neg;
			$rétroactions["feedback_err"] = $question->feedback_err;
			$tentativeTraitée = $this->traiterTentativeProg($tentative, $rétroactions, $tests);
			return $tentativeTraitée;
		}

		return null;
	}

	private function exécuter_prog($exécutable, $testsQuestion)
	{
		return (new ExécuterProgInt())->exécuter($exécutable, $testsQuestion);
	}

	private function traiterTentativeProg($tentative, $rétroactions, $tests)
	{
		return (new TraiterTentativeProgInt())->traiter_résultats($tentative, $rétroactions, $tests);
	}
}
