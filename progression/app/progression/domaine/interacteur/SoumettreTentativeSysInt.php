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

use progression\domaine\entité\{Avancement, Question};

class SoumettreTentativeSysInt extends Interacteur
{
	public function soumettre_tentative($username, $question, $tentative)
	{
		$tentativeTraitée = null;

		if ($question->solution) {
			if ($this->vérifier_réponse_courte($question, $tentative)) {
				$tentative->réussi = true;
				$tentative->tests_réussis = 1;
				$tentative->feedback = $question->feedback_pos;
			} else {
				$tentative->réussi = false;
				$tentative->tests_réussis = 0;
				$tentative->feedback = $question->feedback_neg;
			}
			$tentative->temps_exécution = 0;
			$tentativeTraitée = $tentative;
		}

		if ($question->tests != null && count($question->tests) > 0) {
			$tentative = $this->exécuter_validation($question, $tentative);
			$rétroactions["feedback_pos"] = $question->feedback_pos;
			$rétroactions["feedback_neg"] = $question->feedback_neg;
			$tentativeTraitée = $this->traiter_tentative_sys($tentative, $rétroactions, $question->tests);
		}

		return $tentativeTraitée;
	}

	private function vérifier_réponse_courte($question, $tentative)
	{
		$valide = false;

		if ($question->solution[0] == "~" && $question->solution[strlen($question->solution) - 1] == "~") {
			if (preg_match($question->solution, $tentative->réponse)) {
				$valide = true;
			}
		} elseif ($question->solution == $tentative->réponse) {
			$valide = true;
		}
		return $valide;
	}

	private function exécuter_validation($question, $tentative)
	{
		$résultats = $this->exécuter_sys($question, $tentative);
		$tentative->temps_exécution = $résultats["temps_exécution"];
		$tentative->résultats = $résultats["résultats"];

		return $tentative;
	}

	private function exécuter_sys($question, $tentative)
	{
		return (new ExécuterSysInt())->exécuter($question, $tentative);
	}

	private function traiter_tentative_sys($tentative, $rétroactions, $tests)
	{
		return (new TraiterTentativeSysInt())->traiter_résultats($tentative, $rétroactions, $tests);
	}
}
