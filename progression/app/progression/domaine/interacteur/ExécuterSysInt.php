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

use progression\domaine\entité\{Résultat, TestSys};
use Illuminate\Support\Facades\Log;

class ExécuterSysInt extends Interacteur
{
	/**
	 * @param array<String> $conteneur
	 * @param array<TestSys> $tests
	 * @return mixed
	 */
	public function exécuter(string $utilisateur, string $image, array|Null $conteneur, array $tests): mixed
	{
		$comp_resp = $this->source_dao
			->get_exécuteur()
			->exécuter_sys($utilisateur, $image, $conteneur ? $conteneur["id"] : "", $tests);
		if (!$comp_resp) {
			return null;
		}
		$réponse = [];
		$résultats = null;

		$réponse["temps_exécution"] = intval(($comp_resp["temps_exec"] ?? 0) * 1000);
        $réponse["résultats"]=[];
        for($i=0; $i < count($tests); $i++) {
            if( $i < count($comp_resp["résultats"]) ){
                $résultat = $comp_resp["résultats"][$i];
                $réponse["résultats"][] = new Résultat(
                    sortie_observée: $résultat["output"] ?? "",
                    sortie_erreur: $résultat["errors"] ?? "",
                    résultat: false,
                    feedback: null,
                    temps_exécution: intval($résultat["time"] * 1000),
                    code_erreur: $résultat["code"] ?? 0
                );
            }
            else {
                $réponse["résultats"][] = new Résultat();
            }
        }

		$réponse["conteneur"] = [
			"id" => $comp_resp["conteneur"]["id"],
			"ip" => $comp_resp["conteneur"]["ip"],
			"port" => $comp_resp["conteneur"]["port"],
		];

		return $réponse;
	}
}
