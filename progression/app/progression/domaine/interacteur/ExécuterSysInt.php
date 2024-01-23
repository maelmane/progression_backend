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

use progression\domaine\entité\{Résultat, TestSys, TentativeSys};
use progression\domaine\entité\question\QuestionSys;

class ExécuterSysInt extends Interacteur
{
	/**
	 * @param array<TestSys> $tests
	 * @return array<mixed>
	 */
	public function exécuter(QuestionSys $question, TentativeSys $tentative, array $tests, int|null $test_index): array
	{
		$comp_resp = $this->source_dao
			->get_exécuteur()
			->exécuter_sys(
				$question->utilisateur,
				$question->image,
				$tentative->conteneur_id,
				$question->init,
				$tests,
				$test_index,
				$question->commande,
			);

		$réponse = [];
		$résultats = [];

		$réponse["temps_exécution"] = intval($comp_resp["temps_exécution"] * 1000);

		foreach ($comp_resp["résultats"] as $résultat) {
			$résultats[] = new Résultat(
				sortie_observée: $résultat["output"] ?? null,
				sortie_erreur: $résultat["errors"] ?? null,
				résultat: false,
				feedback: null,
				temps_exécution: intval($résultat["time"] * 1000),
				code_retour: $résultat["code"],
			);
		}

		$réponse["résultats"] = $résultats;
		$réponse["conteneur_id"] = $comp_resp["conteneur_id"];
		$réponse["url_terminal"] = $comp_resp["url_terminal"];

		return $réponse;
	}
}
