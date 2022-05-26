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

use mysqli_sql_exception;
use progression\domaine\entité\{Avancement, Question};
use progression\dao\models\{AvancementMdl, UserMdl};
use progression\dao\tentative\{TentativeDAO, TentativeProgDAO, TentativeSysDAO};

class AvancementDAO extends EntitéDAO
{
	public function get_tous($username, $includes = [])
	{
		try {
			return $this->construire(AvancementMdl::where("username", $username)->get(), $includes);
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function get_avancement($username, $question_uri, $includes = [])
	{
		try {
			$data = AvancementMdl::where("username", $username)
				->where("question_uri", $question_uri)
				->first();
			if ($data) {
				return $this->construire([$data], $includes)[$question_uri];
			} else {
				return null;
			}
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function save($username, $question_uri, $avancement)
	{
		try {
			$user_id = UserMdl::where("username", $username)->first()["id"];
			$objet = [];
			$objet["etat"] = $avancement->etat;
			$objet["question_uri"] = $question_uri;
			$objet["username"] = $username;
			$objet["titre"] = $avancement->titre;
			$objet["niveau"] = $avancement->niveau;
			$objet["date_modification"] = $avancement->date_modification;
			$objet["date_reussite"] = $avancement->date_réussite;
			$objet["user_id"] = $user_id;

			return $this->construire([
				AvancementMdl::updateOrCreate(["username" => $username, "question_uri" => $question_uri], $objet),
			])[$question_uri];
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public static function construire($data, $includes = [])
	{
		if ($data == null) {
			return [];
		}

		$avancements = [];
		foreach ($data as $i => $item) {
			$tentatives = [];
			if (in_array("tentatives", $includes)) {
				if ($item["type"] == "prog") {
					$tentatives = TentativeProgDAO::construire($item["tentatives"]);
				} elseif ($item["type"] == "sys") {
					$tentatives = TentativeSysDAO::construire($item["tentatives"]);
				}
			}
			$avancement = new Avancement(
				$tentatives,
				$item["titre"],
				$item["niveau"],
				in_array("sauvegardes", $includes) ? SauvegardeDAO::construire($item["sauvegardes"]) : [],
			);
			$avancement->etat = $item["etat"];
			$avancement->date_modification = $item["date_modification"];
			$avancement->date_réussite = $item["date_reussite"];
			$avancements[$item["question_uri"]] = $avancement;
		}

		return $avancements;
	}
}
