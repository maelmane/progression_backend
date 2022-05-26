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
use progression\util\Encodage;

class AvancementTransformer extends BaseTransformer
{
	public $type = "avancement";

	protected array $availableIncludes = ["tentatives", "sauvegardes"];
	protected array $availableParams = ["fields"];

	public function transform(Avancement $avancement)
	{
		$data_out = [
			"id" => $this->id ."/". Encodage::base64_encode_url($avancement->uri),
			"état" => $avancement->etat,
			"titre" => $avancement->titre,
			"niveau" => $avancement->niveau,
			"date_modification" => $avancement->date_modification,
			"date_réussite" => $avancement->date_réussite,
			"links" => (isset($avancement->links) ? $avancement->links : []) + [
				"self" => "{$_ENV["APP_URL"]}avancement/{$this->id}",
			],
		];

		return $data_out;
	}

	public function includeTentatives($avancement, $params = null)
	{
		$params = $this->validerParams($params);
		$tentatives = $avancement->tentatives;

		foreach ($tentatives as $tentative) {
			$tentative->links = [
				"related" => "{$_ENV["APP_URL"]}avancement/{$this->id}",
			];

			if ($params && array_key_exists("fields", $params)) {
				$tentative = $this->sélectionnerChamps($tentative, $params["fields"]);
			}
		}

		$id = $this->id . "/" . Encodage::base64_encode_url($avancement->uri);
		if (empty($tentative)) {
			return $this->collection($tentatives, new TentativeTransformer($id), "tentative");
		} else {
			if ($tentatives[0] instanceof TentativeProg) {
				return $this->collection($tentatives, new TentativeProgTransformer($id), "tentative");
			} elseif ($tentatives[0] instanceof TentativeSys) {
				return $this->collection($tentatives, new TentativeSysTransformer($id), "tentative");
			} else {
				return $this->collection($tentatives, new TentativeTransformer($id), "tentative");
			}
		}
	}

	public function includeSauvegardes($avancement)
	{
		foreach ($avancement->sauvegardes as $langage => $sauvegarde) {
			$sauvegarde->links = [
				"related" => "{$_ENV["APP_URL"]}avancement/{$this->id}/" . Encodage::base64_encode_url($avancement->uri),
			];
		}

		return $this->collection($avancement->sauvegardes, new SauvegardeTransformer($this->id . "/" . Encodage::base64_encode_url($avancement->uri)), "sauvegarde");
	}
}
