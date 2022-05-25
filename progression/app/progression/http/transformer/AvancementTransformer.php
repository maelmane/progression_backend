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
			"id" => $avancement->id,
			"état" => $avancement->etat,
			"titre" => $avancement->titre,
			"niveau" => $avancement->niveau,
			"date_modification" => $avancement->date_modification,
			"date_réussite" => $avancement->date_réussite,
			"links" => (isset($avancement->links) ? $avancement->links : []) + [
				"self" => "{$_ENV["APP_URL"]}avancement/{$avancement->id}",
			],
		];

		return $data_out;
	}

	public function includeTentatives($avancement, $params = null)
	{
		$params = $this->validerParams($params);
		$tentatives = $avancement->tentatives;

		foreach ($tentatives as $tentative) {
			$tentative->id = "{$avancement->id}/{$tentative->date_soumission}";
			$tentative->links = [
				"related" => "{$_ENV["APP_URL"]}avancement/{$avancement->id}",
			];

			if ($params && array_key_exists("fields", $params)) {
				$tentative = $this->sélectionnerChamps($tentative, $params["fields"]);
			}

			$tentative->id = $id;
			$tentative->links = $links;

			$tentatives[$i] = $tentative;
		}

		if(empty($tentative)){
			return $this->collection($tentatives, new TentativeTransformer(), "tentative");
		}
		else{
			if ($tentatives[0] instanceof TentativeProg){
				return $this->collection($tentatives, new TentativeProgTransformer(), "tentative");
			} elseif ($tentatives[0] instanceof TentativeSys){
				return $this->collection($tentatives, new TentativeSysTransformer(), "tentative");
			} else {
				return $this->collection($tentatives, new TentativeTransformer(), "tentative");
			}
	}

	public function includeSauvegardes($avancement)
	{
		foreach ($avancement->sauvegardes as $langage => $sauvegarde) {
			$sauvegarde->id = "{$avancement->id}/" . $langage;
			$sauvegarde->links = [
				"related" => "{$_ENV["APP_URL"]}avancement/{$avancement->id}",
			];
		}

		return $this->collection($avancement->sauvegardes, new SauvegardeTransformer(), "sauvegarde");
	}
}
