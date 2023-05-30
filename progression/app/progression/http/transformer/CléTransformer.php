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

use progression\domaine\entité\clé\Clé;

class CléTransformer extends BaseTransformer
{
	public $type = "cle";

	public function transform(Clé $clé)
	{
		$data_out = [
			"id" => "{$this->id}/{$clé->id}",
			"secret" => $clé->secret,
			"création" => $clé->création,
			"expiration" => $clé->expiration,
			"portée" => $clé->portée->value,
			"links" => (isset($clé->links) ? $clé->links : []) + [
				"self" => "{$this->urlBase}/cle/{$this->id}/{$clé->id}",
				"user" => "{$this->urlBase}/user/{$this->id}",
			],
		];

		return $data_out;
	}
}
