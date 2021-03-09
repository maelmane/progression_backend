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
use progression\domaine\entité\TentativeProg;

class TentativeProgTransformer extends Fractal\TransformerAbstract
{
	public $type = "tentative";
	protected $availableIncludes = ["resultats"];

	public function transform(TentativeProg $tentative)
	{
		$data_out = [
			"id" => $tentative->id,
			"date_soumission" => $tentative->date_soumission,
			"tests_réussis" => $tentative->tests_réussis,
			"feedback" => $tentative->feedback,
			"langage" => $tentative->langage,
			"code" => $tentative->code,
			"links" => (isset($tentative->links) ? $tentative->links : []) + [
				"self" => "{$_ENV["APP_URL"]}tentative/{$tentative->id}"
			]
		];

		return $data_out;
	}

	public function includeResultats(TentativeProg $tentative)
	{
		foreach ($tentative->résultats as $résultat) {
			$résultat->links = [
				"related" =>
				$_ENV["APP_URL"] .
					"tentative/{$résultat->id}",
			];
		}
		return $this->collection(
			$tentative->résultats,
			new RéponseProgTransformer(),
			"resultats"
		);
	}
}
