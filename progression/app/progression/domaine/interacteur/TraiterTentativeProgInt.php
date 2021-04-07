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
				$nb_tests_réussis++;
			} elseif ($tentative->résultats[$i]->sortie_erreur != "") {
				$tentative->résultats[$i]->feedback = $test->feedback_err;
				$erreur = true;
			} else {
				$tentative->résultats[$i]->feedback = $test->feedback_neg;
			}
		}

		$tentative->tests_réussis = $nb_tests_réussis;

		if ($nb_tests_réussis == count($question->tests)) {
			$tentative->feedback = $question->feedback_pos;
			$tentative->réussi = true;
		} else {
			$tentative->feedback = ($erreur) ? $question->feedback_err : $question->feedback_neg;
			$tentative->réussi = false;
		}

		return $tentative;
	}

	private function vérifier_solution($résultat, $solution)
	{
		$sortie_standard = $résultat->sortie_observée;
		$erreur = $résultat->sortie_erreur;
		return $sortie_standard == $solution && $erreur == "";
	}
}
