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
use progression\domaine\interacteur\ObtenirSauvegardeAutomatiqueInt;
use progression\domaine\interacteur\CréerSauvegardeAutomatiqueInt;
use progression\http\transformer\SauvegardeAutomatiqueTransformer;
use progression\util\Encodage;
use progression\domaine\entité\Sauvegarde;

class SauvegardeCtl extends Contrôleur
{
	public function get(Request $request, $username, $question_uri, $langage)
	{
		print_r(" Là1");
		$chemin = Encodage::base64_decode_url($question_uri);
		$sauvegarde = null;
		$réponse = null;

		$sauvegardeInt = new ObtenirSauvegardeAutomatiqueInt();
		$sauvegarde = $sauvegardeInt->get_sauvegarde_automatique($username, $chemin, $langage);

		if ($sauvegarde != null) {
			//$réponse = $this->item($sauvegarde, new SauvegardeAutomatiqueTransformer());
			$réponse = "La sauvegarde a été correctement récupérée";
		}

		return $this->préparer_réponse($réponse);
	}

	public function post(Request $request, $username, $question_uri)
	{
	}
}
