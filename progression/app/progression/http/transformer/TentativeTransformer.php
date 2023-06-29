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

use League\Fractal\Resource\Collection;
use progression\domaine\entité\Tentative;
use progression\http\transformer\dto\TentativeDTO;

class TentativeTransformer extends BaseTransformer
{
	public $type = "tentative";
	protected array $availableIncludes = ["commentaires"];

	/**
	 * @return array<mixed>
	 */
	public function transform(TentativeDTO $data_in): array
	{
		$id = $data_in->id;
		$tentative = $data_in->objet;
		$liens = $data_in->liens;

		$data_out = [
			"id" => $id,
			"date_soumission" => $tentative->date_soumission,
			"feedback" => $tentative->feedback,
			"réussi" => $tentative->réussi,
			"temps_exécution" => $tentative->temps_exécution,
			"links" => $liens,
		];

		return $data_out;
	}

	public function includeCommentaires(TentativeDTO $data_in): Collection
	{
		$id = $data_in->id;
		$tentative = $data_in->objet;

		return $this->collection($data_in->commentaires, new CommentaireTransformer(), "commentaire");
	}

	public function includeResultats(TentativeDTO $data_in): Collection
	{
		$id = $data_in->id;
		$tentative = $data_in->objet;

		return $this->collection($data_in->résultats, new RésultatTransformer(), "resultat");
	}
}
