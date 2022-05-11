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

namespace progression\http\contrôleur;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use progression\domaine\interacteur\ObtenirAvancementsInt;
use progression\domaine\interacteur\SauvegarderAvancementInt;
use progression\domaine\interacteur\ObtenirQuestionInt;
use progression\http\transformer\AvancementTransformer;
use progression\util\Encodage;
use progression\domaine\entité\Avancement;

class AvancementsCtl extends Contrôleur
{
	public function get(Request $request, $username)
	{
		Log::debug("AvancementsCtl.get. Params : ", [$request->all(), $username]);

		$avancements = $this->obtenir_avancements($username);

		$réponse = $this->valider_et_préparer_réponse($avancements);

		Log::debug("AvancementsCtl.get. Retour : ", [$réponse]);
		return $réponse;
	}

	private function valider_et_préparer_réponse($avancements)
	{
		Log::debug("AvancementsCtl.valider_et_préparer_réponse. Params : ", [$avancements]);

		if ($avancements === null) {
			$réponse = null;
		} else {
			$réponse = [];
			foreach ($avancements as $id => $avancement) {
				$avancement->id = $id;
			}
			$réponse = $this->collection($avancements, new AvancementTransformer());
		}

		$réponse = $this->préparer_réponse($réponse);

		Log::debug("AvancementsCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}

	private function obtenir_avancements($username)
	{
		Log::debug("AvancementsCtl.obtenir_avancements. Params : ", [$username]);

		$avancementsInt = new ObtenirAvancementsInt();

		$avancements = $avancementsInt->get_avancements($username);

		Log::debug("AvancementsCtl.obtenir_avancements. Retour : ", [$avancements]);
		return $avancements;
	}
}
