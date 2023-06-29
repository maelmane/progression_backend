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

use progression\domaine\entité\clé\Portée;
use progression\http\transformer\dto\GénériqueDTO;

class CléTransformer extends BaseTransformer
{
	public $type = "cle";

	public function transform(GénériqueDTO $data_in)
	{
		$id = $data_in->id;
		$clé = $data_in->objet;
		$liens = $data_in->liens;

		$data_out = [
			"id" => "$id",
			"secret" => $clé->secret,
			"création" => $clé->création,
			"expiration" => $clé->expiration,
			"portée" => match ($clé->portée) {
				Portée::RÉVOQUÉE => "révoquée",
				Portée::AUTH => "authentification",
				default => "indéfini",
			},
			"links" => $liens,
		];

		return $data_out;
	}
}
