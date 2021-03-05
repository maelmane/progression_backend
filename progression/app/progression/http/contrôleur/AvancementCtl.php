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
use progression\http\transformer\AvancementProgTransformer;
use progression\domaine\entité\{AvancementProg, AvancementSys, AvancementBD};
use Illuminate\Support\Facades\Log;
use progression\util\Encodage;

class AvancementCtl extends Contrôleur
{
	public function get(Request $request, $username, $uri)
	{
		$chemin = Encodage::base64_decode_url($uri);
		$avancement = null;

		if ($chemin != null && $chemin != "" && $username != null && $username != "") {
			$avancementInt = $this->intFactory->getObtenirAvancementInt();
			$avancement = $avancementInt->get_avancement($username, $chemin);
		}

		if ($avancement != null) {
			$avancement->id = $username . "/{$uri}";
		}

		$réponse = null;

		if ($avancement instanceof AvancementProg) {
			$réponse = $this->item($avancement, new AvancementProgTransformer());
		} elseif ($avancement instanceof AvancementSys) {
			Log::warning("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			return $this->réponse_json(["message" => "Question système non implémentée."], 501);
		} elseif ($avancement instanceof AvancementBD) {
			Log::warning("({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ")");
			return $this->réponse_json(["message" => "Question BD non implémentée."], 501);
		}

		return $this->préparer_réponse($réponse);
	}
}
