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
use progression\domaine\entité\{Avancement, Question};

class AvancementTransformer extends Fractal\TransformerAbstract
{
	public $type = "avancement";

	protected $availableIncludes = ["tentatives", "sauvegardes"];

	public function transform(Avancement $avancement)
	{
		$data_out = [
			"id" => $avancement->id,
			"état" => $avancement->etat,
			"links" => (isset($avancement->links) ? $avancement->links : []) + [
				"self" => "{$_ENV["APP_URL"]}avancement/{$avancement->id}",
				"tentative" => "{$_ENV["APP_URL"]}tentative/{$avancement->id}",
			],
		];

		return $data_out;
	}

	public function includeTentatives($avancement)
	{
		$tentatives = $avancement->tentatives;
		foreach ($tentatives as $tentative) {
			$tentative->id = "{$avancement->id}/{$tentative->date_soumission}";
			$tentative->links = [
				"related" => "{$_ENV["APP_URL"]}avancement/{$avancement->id}",
			];
		}

		if ($avancement->type == Question::TYPE_PROG) {
			return $this->collection($tentatives, new TentativeProgTransformer(), "tentative");
		} elseif ($avancement->type == Question::TYPE_SYS) {
			return $this->collection($tentatives, new TentativeSysTransformer(), "tentative");
		} elseif ($avancement->type == Question::TYPE_BD) {
			return $this->collection($tentatives, new TentativeBDTransformer(), "tentative");
		}
	}

	public function includeSauvegardes($avancement)
	{
		foreach ($avancement->sauvegardes as $langage => $sauvegarde) {
			$sauvegarde->id = "{$avancement->id}/" . $langage;
			$sauvegarde->links = [
				"self" => "{$_ENV["APP_URL"]}sauvegarde/{$sauvegarde->id}",
				"related" => "{$_ENV["APP_URL"]}avancement/{$avancement->id}",
			];
		}

		return $this->collection($avancement->sauvegardes, new SauvegardeAutomatiqueTransformer(), "sauvegarde");
	}
}
