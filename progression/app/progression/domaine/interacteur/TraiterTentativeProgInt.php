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

use progression\domaine\entité\{Question, TentativeProg};

class TraiterTentativeProgInt extends Interacteur
{
	function traiter_résultats($résultats, $tests)
	{
		$nb_tests_réussis = 0;
		foreach ($tests as $i => $test) {
			if ($this->vérifier_solution($test->sorties, $test->sortie_attendue)) {
				$résultats[$i]->résultat = true;
				$nb_tests_réussis++;
			}
		}

		$résultats["tests_réussis"] = $nb_tests_réussis;
		$résultats["résultat_prog"] = $résultats;

		return $résultats;
	}

	private function vérifier_solution($sorties, $solution)
	{
		$sortie_standard = $sorties["output"];
		$erreur = $sorties["erreurs"];
		return $sortie_standard == $solution && $erreur == "";
	}
}
