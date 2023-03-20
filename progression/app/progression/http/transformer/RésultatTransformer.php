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

use progression\domaine\entité\Résultat;

class RésultatTransformer extends BaseTransformer
{
	public $type = "resultat";

	public function transform(Résultat $réponse)
	{
		$data = [
			"id" => "{$this->id}/{$réponse->id}",
			"sortie_observée" => $réponse->sortie_observée,
			"sortie_erreur" => $réponse->sortie_erreur,
			"résultat" => $réponse->résultat,
			"feedback" => $réponse->feedback,
			"temps_exec" => $réponse->temps_exécution,
			"code_erreur" => $réponse->code_erreur,
			"links" => (isset($réponse->links) ? $réponse->links : []) + [
				"tentative" => "{$_ENV["APP_URL"]}tentative/{$this->id}",
				"self" => "{$_ENV["APP_URL"]}resultat/{$this->id}/{$réponse->id}",
			],
		];

		return $data;
	}
}
