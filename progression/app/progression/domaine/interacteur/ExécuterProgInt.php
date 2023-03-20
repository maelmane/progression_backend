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

use progression\domaine\entité\Résultat;

class ExécuterProgInt extends Interacteur
{
	public function exécuter($exécutable, $tests)
	{
		$comp_resp = $this->source_dao->get_exécuteur()->exécuter_prog($exécutable, $tests);
		if (!$comp_resp) {
			return null;
		}
		$réponse = [];
		$résultats = null;

		$réponse["temps_exécution"] = intval($comp_resp["temps_exec"] * 1000);

		foreach ($comp_resp["résultats"] as $résultat) {
			$résultats[] = new Résultat(
				sortie_observée: $résultat["output"],
				sortie_erreur: $résultat["errors"],
				résultat: false,
				feedback: null,
				temps_exécution: intval($résultat["time"] * 1000),
                code_erreur: $résultat["code_erreur"] ?? 0
			);
		}
		$réponse["résultats"] = $résultats;
		return $réponse;
	}
}
