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
use progression\http\transformer\TentativeTransformer;
use progression\util\Encodage;

class TentativeCtl extends Contrôleur
{
	public function get(Request $request, $username, $question, $timestamp)
	{
		$chemin = Encodage::base64_decode_url($question);
		$tentative = null;

		if ($chemin != null && $chemin != "" && $username != null && $username != "" && $timestamp != null) {
			$avancementInt = $this->intFactory->getObtenirAvancementInt();
			$questionInt = $this->intFactory->getObtenirQuestionInt();

			//$questionID = $questionInt->get_question($chemin)->id;
			//$tentative = $avancementInt->get_tentative($username, 2, $timestamp);
			$tentative = $avancementInt->get_tentative(1, 2, 1614711760);
		}

		$réponse = $this->item($tentative, new TentativeTransformer(), "tentative");

		return $this->préparer_réponse($réponse);
	}
}
