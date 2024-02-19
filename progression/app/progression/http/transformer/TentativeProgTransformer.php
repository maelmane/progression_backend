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
use progression\domaine\entité\{Tentative, TentativeProg};
use progression\http\transformer\dto\TentativeDTO;

class TentativeProgTransformer extends BaseTransformer
{
	public $type = "tentative";
	protected array $availableIncludes = ["resultats", "commentaires"];

	/**
	 * @return array<mixed>
	 */
	public function transform(TentativeDTO $data_in): array
	{
		$id = $data_in->id;
		$tentative = $data_in->objet;
		$liens = $data_in->liens;

		$data_out = (new TentativeTransformer())->transform($data_in);
		$data_out = array_merge($data_out, [
			"sous_type" => "tentativeProg",
			"langage" => $tentative->langage,
			"code" => $tentative->code,
			"tests_réussis" => $tentative->tests_réussis,
		]);
		return $data_out;
	}

	public function includeResultats(TentativeDTO $data_in): Collection
	{
		return (new TentativeTransformer())->includeResultats($data_in);
	}

	public function includeCommentaires(TentativeDTO $data_in): Collection
	{
		return (new TentativeTransformer())->includeCommentaires($data_in);
	}
}
