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

.	You should have received a copy of the GNU General Public License
	along with Progression.  If not, see <https://www.gnu.org/licenses/>.
*/

namespace progression\http\contrôleur;

use Illuminate\Http\Request;
use progression\domaine\interacteur\ObtenirAvancementInt;
use progression\http\transformer\AvancementTransformer;
use progression\util\Encodage;

class AvancementCtl extends Contrôleur
{
	public function get(Request $request, $username, $question_uri)
	{
		$chemin = Encodage::base64_decode_url($question_uri);
		$avancement = null;
		$avancementInt = new ObtenirAvancementInt();
		$avancement = $avancementInt->get_avancement($username, $chemin);

		$réponse = null;

		if ($avancement != null) {
			$avancement->id = "{$username}/$question_uri";

			$réponse = $this->item($avancement, new AvancementTransformer());
		}

		return $this->préparer_réponse($réponse);
	}
}
