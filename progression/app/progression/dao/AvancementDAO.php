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

namespace progression\dao;

use progression\domaine\entité\Avancement;
use progression\dao\models\{AvancementMdl, UserMdl};
use progression\dao\tentative\{TentativeProgDAO, TentativeSysDAO};
use Illuminate\Database\QueryException;
use progression\domaine\interacteur\IntégritéException;

class AvancementDAO extends EntitéDAO
{
	/**
	 * @return array<Avancement>
	 */
	public function get_tous($username, $includes = []): array
	{
		try {
			return $this->construire(
				AvancementMdl::select("avancement.*")
					->with(in_array("tentatives", $includes) ? ["tentatives_prog", "tentatives_sys"] : [])
					->with(in_array("sauvegardes", $includes) ? ["sauvegardes"] : [])
					->join("user", "avancement.user_id", "=", "user.id")
					->where("user.username", $username)
					->get(),
				$includes,
			);
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function get_avancement($username, $question_uri, $includes = []): Avancement|null
	{
		try {
			$data = AvancementMdl::select("avancement.*")
				->with(in_array("tentatives", $includes) ? ["tentatives_prog", "tentatives_sys"] : [])
				->with(in_array("sauvegardes", $includes) ? ["sauvegardes"] : [])
				->join("user", "avancement.user_id", "=", "user.id")
				->where("user.username", $username)
				->where("avancement.question_uri", $question_uri)
				->first();

			return self::premier_élément($this->construire([$data], $includes));
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	/**
	 * @return array<Avancement>
	 */
	public function save(string $username, string $question_uri, string $type, Avancement $avancement): array
	{
		try {
			$user = UserMdl::query()
				->where("username", $username)
				->first();

			if (!$user) {
				throw new IntégritéException("Impossible de sauvegarder la ressource; le parent n'existe pas.");
			}

			$objet = [
				"question_uri" => $question_uri,
				"état" => $avancement->état,
				"type" => $type,
				"titre" => $avancement->titre,
				"niveau" => $avancement->niveau,
				"date_modification" => $avancement->date_modification,
				"date_reussite" => $avancement->date_réussite,
				"extra" => $avancement->extra,
			];

			return $this->construire([
				AvancementMdl::updateOrCreate(["user_id" => $user["id"], "question_uri" => $question_uri], $objet),
			]);
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public static function construire($data, $includes = [])
	{
		$avancements = [];
		foreach ($data as $item) {
			$tentatives = [];
			if ($item == null) {
				continue;
			}
			if ($includes) {
				if ($item["type"] == "prog") {
					$tentatives = in_array("tentatives", $includes)
						? TentativeProgDAO::construire(
							$item["tentatives_prog"],
							self::filtrer_niveaux($includes, "tentatives"),
						)
						: [];
				} elseif ($item["type"] == "sys") {
					$tentatives = in_array("tentatives", $includes)
						? TentativeSysDAO::construire(
							$item["tentatives_sys"],
							self::filtrer_niveaux($includes, "tentatives"),
						)
						: [];
				}
			}
			$avancement = new Avancement(
				tentatives: $tentatives,
				titre: $item["titre"],
				niveau: $item["niveau"],
				sauvegardes: in_array("sauvegardes", $includes)
					? SauvegardeDAO::construire($item["sauvegardes"], self::filtrer_niveaux($includes, "sauvegardes"))
					: [],
			);
			$avancement->état = $item["état"];
			$avancement->date_modification = $item["date_modification"];
			$avancement->date_réussite = $item["date_reussite"];
			$avancement->extra = $item["extra"];
			$avancements[$item["question_uri"]] = $avancement;
		}
		return $avancements;
	}
}
