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

class TraiterTentativeProgInt extends Interacteur
{
	function traiter_résultats($question, $tentative)
	{
		$nb_tests_réussis = 0;
		$erreur = false;
		foreach ($question->tests as $i => $test) {
			if ($this->vérifier_solution($tentative->résultats[$i], $test->sortie_attendue)) {
				$tentative->résultats[$i]->feedback = $test->feedback_pos;
				$tentative->résultats[$i]->résultat = true;
				$tentative->résultats[$i]->feedback = $test->feedback_pos;
				$nb_tests_réussis++;
			} else {
				$tentative->résultats[$i]->résultat = false;
				$tentative->résultats[$i]->feedback = $test->feedback_neg;
				if ($tentative->résultats[$i]->sortie_erreur) {
					$erreur = true;
					if ($test->feedback_err) {
						$tentative->résultats[$i]->feedback = $test->feedback_err;
					}
				}
			}
		}

		$tentative->tests_réussis = $nb_tests_réussis;

		if ($erreur) {
			$tentative->réussi = false;
			if ($question->feedback_err) {
				$tentative->feedback = $question->feedback_err;
			} else {
				$tentative->feedback = $question->feedback_neg;
			}
		} elseif ($nb_tests_réussis == count($question->tests)) {
			$tentative->réussi = true;
			$tentative->feedback = $question->feedback_pos;
		} else {
			$tentative->réussi = false;
			$tentative->feedback = $question->feedback_neg;
		}

		return $tentative;
	}

	private function vérifier_solution($résultat, $solution)
	{
		return $résultat->sortie_observée == $solution && !$résultat->sortie_erreur;
	}
}
