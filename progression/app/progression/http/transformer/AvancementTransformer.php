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

use progression\domaine\entité\{Avancement, TentativeProg, TentativeSys};
use progression\domaine\entité\question\État;

use progression\http\transformer\dto\AvancementDTO;

class AvancementTransformer extends BaseTransformer
{
	public $type = "avancement";

	protected array $availableIncludes = ["tentatives", "sauvegardes"];
	protected array $availableParams = ["fields"];

	public function transform(AvancementDTO $data_in)
	{
		$id = $data_in->id;
		$avancement = $data_in->objet;
		$avancement = (fn($avancement): Avancement => $avancement)($avancement);
		$liens = $data_in->liens;

		$data_out = [
			"id" => $id,
			"état" => match ($avancement->état) {
				État::DEBUT => "début",
				État::NONREUSSI => "non_réussi",
				État::REUSSI => "réussi",
				default => "indéfini",
			},
			"titre" => $avancement->titre,
			"niveau" => $avancement->niveau,
			"date_modification" => $avancement->date_modification,
			"date_réussite" => $avancement->date_réussite,
			"extra" => $avancement->extra,
			"links" => $liens,
		];

		return $data_out;
	}

	public function includeTentatives(AvancementDTO $data_in, $params = null)
	{
		$id = $data_in->id;
		$avancement = $data_in->objet;

		$params = $this->validerParams($params);

		$tentatives = $avancement->tentatives;
		foreach ($tentatives as $date_soumission => $tentative) {
			if ($params && array_key_exists("fields", $params)) {
				$tentative = $this->sélectionnerChamps($tentative, $params["fields"]);
			}
		}

		if (empty($tentatives)) {
			return $this->collection([], new TentativeTransformer(), "tentative");
		} else {
			if ($tentatives[array_key_first($tentatives)] instanceof TentativeProg) {
				return $this->collection($data_in->tentatives, new TentativeProgTransformer(), "tentative");
			} elseif ($tentatives[array_key_first($tentatives)] instanceof TentativeSys) {
				return $this->collection($data_in->tentatives, new TentativeSysTransformer(), "tentative");
			} else {
				return $this->collection($data_in->tentatives, new TentativeTransformer(), "tentative");
			}
		}
	}

	public function includeSauvegardes(AvancementDTO $data_in)
	{
		$id = $data_in->id;
		$avancement = $data_in->objet;

		return $this->collection($data_in->sauvegardes, new SauvegardeTransformer(), "sauvegarde");
	}
}
