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

use League\Fractal;
use progression\domaine\entité\Commentaire;

class CommentaireTransformer extends Fractal\TransformerAbstract
{
	public $type = "commentaire";

	public function transform(Commentaire $commentaire)
	{
		$data_out = [
			"id" => $commentaire->id,
			"message" => $commentaire->message,
			"créateur" => $commentaire->créateur,
            "date" => $commentaire->date,
			"numéro_ligne" => $commentaire->numéro_ligne,
			"links" => (isset($commentaire->links) ? $commentaire->links : []) + [
				"self" => "{$_ENV["APP_URL"]}commentaire/{$commentaire->id}",
			],
		];

		return $data_out;
	}
}
