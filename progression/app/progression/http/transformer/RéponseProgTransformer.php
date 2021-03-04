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

namespace progression\http\transformer;

use progression\domaine\entité\RéponseProg;
use League\Fractal;

class RéponseProgTransformer extends Fractal\TransformerAbstract
{
	public $type = "reponse";

	public function transform(RéponseProg $réponse)
	{
		$data = [
			"id" => $réponse->id,
			"numéro" => $réponse->numéro,
			"sortie_observée" => $réponse->sortie_observée,
			"sortie_erreur" => $réponse->sortie_erreur,
			"résultat" => $réponse->résultat,
			"feedback" => $réponse->feedback,
			"links" => [
				"self" => "{$_ENV["APP_URL"]}reponse/{$réponse->id}",
			],
		];

		return $data;
	}
}
