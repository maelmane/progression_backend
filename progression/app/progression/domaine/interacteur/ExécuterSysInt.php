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

use progression\domaine\entité\RésultatSys;

class ExécuterSysInt extends Interacteur
{
	public function exécuter($question, $tentative)
	{
		$comp_resp = $this->source_dao->get_exécuteur()->exécuter_sys($question, $tentative);
		if (!$comp_resp) {
			return null;
		}
		$réponse = [];
		$résultats = null;

		$réponse["temps_exécution"] = intval($comp_resp["temps_exec"] * 1000);

		foreach ($comp_resp["résultats"] as $résultat) {
			$résultats[] = new RésultatSys($résultat["output"], false, null, intval($résultat["time"] * 1000));
		}
		foreach ($comp_resp["conteneur"] as $conteneur) {
			$réponse["id_conteneur"] = $conteneur["id"];
			$réponse["ip_conteneur"] = $conteneur["ip"];
			$réponse["port_conteneur"] = $conteneur["port"];
		}

		$réponse["résultats"] = $résultats;
		return $réponse;
	}
}
