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
		$tentative = null;

		if ($question != null && $question != "" && $username != null && $username != "" && $timestamp != null) {
			$avancementInt = $this->intFactory->getObtenirAvancementInt();

			$tentative = $avancementInt->get_tentative($username, $question, $timestamp);
		}
		$tentative->user_id = $username;
		$tentative->question_id = $question;
		$réponse = $this->item($tentative, new TentativeTransformer());

		return $this->préparer_réponse($réponse);
	}
}
