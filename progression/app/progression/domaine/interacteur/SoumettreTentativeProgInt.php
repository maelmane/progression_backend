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

use progression\domaine\entité\{RésultatProg, TentativeBD, TentativeProg, TentativeSys};

class SoumettreTentativeProgInt extends Interacteur
{

	public function soumettre_tentative($username, $question, $tentative)
	{
		$intFactory = new InteracteurFactory();
		$exécutable = null;

		$préparerProgInt = $intFactory->getPréparerProgInt();
		$exécutable = $préparerProgInt->préparer_exécutable($question, $tentative);

		if ($exécutable) {
			$exécuterProgInt = $intFactory->getExécuterProgInt();

			foreach ($question->tests as $i => $test) {
				$résultat = $exécuterProgInt->exécuter($exécutable, $test);
				$tentative->résultats[$i] = $résultat;
			}

			$traiterTentativeProgInt = $intFactory->getTraiterTentativeProgInt();
			$tentativeTraité = $traiterTentativeProgInt->traiter_résultats($question, $tentative);

			$sauvegarderTentativeProgInt = $intFactory->getSauvegarderAvancementProgIntTests();
			// $sauvegarderTentativeProgInt->save($tentativeTraité, $username, $question->uri);

			return $tentativeTraité;
		}

		return null;
	}
}
