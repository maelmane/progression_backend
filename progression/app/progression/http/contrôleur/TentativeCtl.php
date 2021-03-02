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

class TentativeCtl extends Contrôleur
{
	public function get(Request $request, $username, $question, $timestamp)
	{
		$chemin = base64_decode($question);
		$tentative = null;

		if ($chemin != null && $chemin != "" && $username != null && $username != "" && $timestamp != null) {
			$avancementProgInt = $this->intFactory->getObtenirAvancementInt($username);

			$tentative = $avancementProgInt->get_tentative($username, $chemin, $timestamp);
		}

		$réponse = $this->item($tentative, new TentativeTransformer(), "tentative");

		return $this->préparer_réponse($réponse);
	}
}
