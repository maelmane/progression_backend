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

use progression\domaine\entité\Tentative;

class TentativeTransformer extends BaseTransformer
{
	public $type = "tentative";
	protected array $availableIncludes = ["commentaires"];

	public function transform(Tentative $tentative)
	{
		$data_out = [
			"id" => "{$this->id}/{$tentative->id}",
			"date_soumission" => $tentative->date_soumission,
			"feedback" => $tentative->feedback,
			"réussi" => $tentative->réussi,
			"temps_exécution" => $tentative->temps_exécution,
			"links" => (isset($tentative->links) ? $tentative->links : []) + [
				"self" => "{$_ENV["APP_URL"]}tentative/{$this->id}/{$tentative->id}",
			],
		];

		return $data_out;
	}

	public function includeCommentaires(Tentative $tentative)
	{
		$commentaires = $tentative->commentaires;

		$id_parent = "{$this->id}/{$tentative->id}";

		foreach ($commentaires as $numéro => $commentaire) {
			$commentaire->id = $numéro;
			$commentaire->links = [
				"related" => "{$_ENV["APP_URL"]}tentative/{$id_parent}",
			];
		}
		return $this->collection($commentaires, new CommentaireTransformer($id_parent), "commentaire");
	}
}
