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

namespace progression\dao\tentative;

use Illuminate\Database\QueryException;
use progression\dao\{DAOException, CommentaireDAO};
use progression\domaine\entité\TentativeProg;
use progression\dao\models\{TentativeProgMdl, AvancementMdl};

class TentativeProgDAO extends TentativeDAO
{
	public function get_toutes($username, $question_uri, $includes = [])
	{
		try {
			return $this->construire(
				TentativeProgMdl::select("reponse_prog.*")
					->with($includes)
					->join("avancement", "reponse_prog.avancement_id", "=", "avancement.id")
					->join("user", "avancement.user_id", "=", "user.id")
					->where("user.username", $username)
					->where("avancement.question_uri", $question_uri)
					->get(),
				$includes,
			);
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function get_tentative($username, $question_uri, $date_soumission, $includes = [])
	{
		try {
			$tentative = TentativeProgMdl::select("reponse_prog.*")
				->with($includes)
				->join("avancement", "reponse_prog.avancement_id", "=", "avancement.id")
				->join("user", "avancement.user_id", "=", "user.id")
				->where("user.username", $username)
				->where("avancement.question_uri", $question_uri)
				->where("date_soumission", $date_soumission)
				->first();

			return $tentative ? $this->construire([$tentative], $includes)[$date_soumission] : null;
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function save($username, $question_uri, $tentative)
	{
		try {
			$avancement = AvancementMdl::select("avancement.id")
				->from("avancement")
				->join("user", "avancement.user_id", "=", "user.id")
				->where("user.username", $username)
				->where("question_uri", $question_uri)
				->first();

            if (!$avancement) return null;
            
			$objet = [
				"langage" => $tentative->langage,
				"code" => $tentative->code,
				"date_soumission" => $tentative->date_soumission,
				"reussi" => $tentative->réussi,
				"tests_reussis" => $tentative->tests_réussis,
				"temps_exécution" => $tentative->temps_exécution,
			];

			return $this->construire([
				TentativeProgMdl::updateOrCreate(
					["avancement_id" => $avancement["id"], "date_soumission" => $tentative->date_soumission],
					$objet,
				),
			])[$tentative->date_soumission];
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public static function construire($data, $includes = [])
	{
		if ($data == null) {
			return [];
		}

		$tentatives = [];
		foreach ($data as $item) {
			$tentative = new TentativeProg(
				langage: $item["langage"],
				code: $item["code"],
				date_soumission: $item["date_soumission"],
				réussi: $item["reussi"],
				résultats: [],
				tests_réussis: $item["tests_reussis"],
				temps_exécution: $item["temps_exécution"],
				feedback: null,
				commentaires: in_array("commentaires", $includes)
					? CommentaireDAO::construire($item["commentaires"])
					: [],
			);
			$tentatives[$item["date_soumission"]] = $tentative;
		}

		return $tentatives;
	}
}
