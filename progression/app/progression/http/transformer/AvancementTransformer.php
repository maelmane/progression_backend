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

use progression\domaine\entité\{Avancement, Question};

class AvancementTransformer extends BaseTransformer
{
	public $type = "avancement";

	protected array $availableIncludes = ["tentatives", "sauvegardes"];
	protected array $availableParams = ["fields"];

	public function transform(Avancement $avancement)
	{
		$data_out = [
			"id" => "{$this->id}/{$avancement->id}",
			"état" => $avancement->etat,
			"titre" => $avancement->titre,
			"niveau" => $avancement->niveau,
			"date_modification" => $avancement->date_modification,
			"date_réussite" => $avancement->date_réussite,
			"links" => (isset($avancement->links) ? $avancement->links : []) + [
				"self" => "{$_ENV["APP_URL"]}avancement/{$this->id}/{$avancement->id}",
			],
		];

		return $data_out;
	}

	public function includeTentatives($avancement, $params = null)
	{
		$params = $this->validerParams($params);
		$tentatives = $avancement->tentatives;

		$id_parent = "{$this->id}/{$avancement->id}";
        
		foreach ($tentatives as $tentative) {
            $tentative->id = $tentative->date_soumission;
			$tentative->links = [
				"related" => "{$_ENV["APP_URL"]}avancement/{$id_parent}",
			];

			if ($params && array_key_exists("fields", $params)) {
				$tentative = $this->sélectionnerChamps($tentative, $params["fields"]);
			}
		}

		if (empty($tentatives)) {
			return $this->collection($tentatives, new TentativeTransformer($id_parent), "tentative");
		} else {
			if ($tentatives[0] instanceof TentativeProg) {
				return $this->collection($tentatives, new TentativeProgTransformer($id_parent), "tentative");
			} elseif ($tentatives[0] instanceof TentativeSys) {
				return $this->collection($tentatives, new TentativeSysTransformer($id_parent), "tentative");
			} else {
				return $this->collection($tentatives, new TentativeTransformer($id_parent), "tentative");
			}
		}
	}

	public function includeSauvegardes($avancement)
	{
		$id_parent = "{$this->id}/{$avancement->id}";

		foreach ($avancement->sauvegardes as $langage => $sauvegarde) {
            $sauvegarde->id = $langage;
			$sauvegarde->links = [
				"related" => "{$_ENV["APP_URL"]}avancement/{$id_parent}";
			];
		}

		return $this->collection($avancement->sauvegardes, new SauvegardeTransformer($id_parent), "sauvegarde");
	}
}
