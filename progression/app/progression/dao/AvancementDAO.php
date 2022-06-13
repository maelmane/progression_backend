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

use progression\domaine\entité\{Avancement, Question};
use progression\dao\models\{AvancementMdl, UserMdl};
use progression\dao\tentative\{TentativeDAO, TentativeProgDAO, TentativeSysDAO};
use Illuminate\Database\QueryException;

class AvancementDAO extends EntitéDAO
{
	public function get_tous($username, $includes = [])
	{
		try {
			return $this->construire(
				AvancementMdl::select("avancement.*")
					->with($includes)
					->join("user", "avancement.user_id", "=", "user.id")
					->where("user.username", $username)
					->get(),
				$includes,
			);
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function get_avancement($username, $question_uri, $includes = [])
	{
		try {
			$data = AvancementMdl::select("avancement.*")
				->with($includes)
				->join("user", "avancement.user_id", "=", "user.id")
				->where("user.username", $username)
				->where("avancement.question_uri", $question_uri)
				->first();
			return $data ? $this->construire([$data], $includes)[$question_uri] : null;
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function save($username, $question_uri, $avancement)
	{
		try {
			$user = UserMdl::query()
				->where("username", $username)
				->first();

            if(!$user) return null;
            
			$objet = [];
			$objet["etat"] = $avancement->etat;
			$objet["question_uri"] = $question_uri;
			$objet["titre"] = $avancement->titre;
			$objet["niveau"] = $avancement->niveau;
			$objet["date_modification"] = $avancement->date_modification;
			$objet["date_reussite"] = $avancement->date_réussite;

			return $this->construire([
				AvancementMdl::updateOrCreate(["user_id" => $user["id"], "question_uri" => $question_uri], $objet),
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
			if (in_array("tentatives_prog", $includes)) {
				$tentatives = TentativeProgDAO::construire($item["tentatives_prog"]);
			}
			if (in_array("tentatives_sys", $includes)) {
				$tentatives = TentativeSysDAO::construire($item["tentatives_sys"]);
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
