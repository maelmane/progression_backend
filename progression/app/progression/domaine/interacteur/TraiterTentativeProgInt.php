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
	function traiter_résultats($tentative, $rétroactions, $tests)
	{
		$nb_tests_réussis = 0;
		$erreur = false;

		foreach ($tests as $i => $test) {
			$hash = array_keys($tentative->résultats)[$i];
			if ($this->vérifier_solution($tentative->résultats[$hash], $test->sortie_attendue)) {
				$tentative->résultats[$hash]->feedback = $test->feedback_pos;
				$tentative->résultats[$hash]->résultat = true;
				$nb_tests_réussis++;
			} else {
				$tentative->résultats[$hash]->résultat = false;
				if ($tentative->résultats[$hash]->sortie_erreur) {
					$erreur = true;
					if ($test->feedback_err) {
						$tentative->résultats[$hash]->feedback = $test->feedback_err;
					}
				} else {
					$tentative->résultats[$hash]->feedback = $test->feedback_neg;
				}
			}
		}
		$tentative->tests_réussis = $nb_tests_réussis;
		if ($erreur) {
			$tentative->réussi = false;
			if ($rétroactions["feedback_err"]) {
				$tentative->feedback = $rétroactions["feedback_err"];
			} elseif (date("j n") === "1 4") {
				$tentative->feedback = (new PatenaudeCitationInt())->get_citation();
			}
		} elseif ($nb_tests_réussis == count($tests)) {
			$tentative->réussi = true;
			$tentative->feedback = $rétroactions["feedback_pos"];
		} else {
			$tentative->réussi = false;
			$tentative->feedback = $rétroactions["feedback_neg"];
		}
		return $tentative;
	}

	private function vérifier_solution($résultat, $solution)
	{
		return $résultat->sortie_observée === $solution && !$résultat->sortie_erreur;
	}
}
