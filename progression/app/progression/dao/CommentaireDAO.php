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

use Illuminate\Database\QueryException;
use progression\domaine\entité\Commentaire;
use progression\dao\models\{TentativeProgMdl, CommentaireMdl, UserMdl};

class CommentaireDAO extends EntitéDAO
{
	public function get_commentaire($id, $includes = ["créateur"])
	{
		try {
			$commentaire = CommentaireMdl::select("commentaire.*")
				->with($includes)
				->where("id", $id)
				->first();

			return $commentaire ? $this->construire([$commentaire], $includes)[$id] : null;
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function get_commentaires_par_tentative($username, $question_uri, $date_soumission, $includes = ["créateur"])
	{
		try {
			return $this->construire(
				CommentaireMdl::select("commentaire.*")
					->with($includes)
					->join("reponse_prog", "tentative_id", "=", "reponse_prog.id")
					->join("avancement", "reponse_prog.avancement_id", "=", "avancement.id")
					->join("user", "avancement.user_id", "=", "user.id")
					->where("user.username", $username)
					->where("avancement.question_uri", $question_uri)
					->where("reponse_prog.date_soumission", $date_soumission)
					->get(),
				$includes,
			);
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function save($username, $question_uri, $date_soumission, $numéro, $commentaire)
	{
		try {
			$tentative = TentativeProgMdl::select("reponse_prog.id")
				->from("reponse_prog")
				->join("avancement", "reponse_prog.avancement_id", "=", "avancement.id")
				->join("user", "avancement.user_id", "=", "user.id")
				->where("user.username", $username)
				->where("avancement.question_uri", $question_uri)
				->first();

			if (!$tentative) {
				return null;
			}

			$créateur = UserMdl::select("user.id")
				->from("user")
				->where("user.username", $commentaire->créateur->username)
				->first();

			if (!$créateur) {
				return null;
			}

			$objet = [
				"tentative_id" => $tentative["id"],
				"créateur_id" => $créateur["id"],
				"message" => $commentaire->message,
				"date" => $commentaire->date,
				"numéro_ligne" => $commentaire->numéro_ligne,
			];
			return $this->construire(
				[CommentaireMdl::updateOrCreate(["id" => $numéro], $objet)],
				["créateur"],
			)[$numéro];
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public static function construire($data, $includes = [])
	{
		$commentaires = [];
		foreach ($data as $i => $item) {
			$id = $item["id"];
			$créateur = in_array("créateur", $includes) ? UserDAO::construire([$item["créateur"]])[0] : null;
			$commentaire = new Commentaire(
				message: $item["message"],
				créateur: $créateur,
				date: $item["date"],
				numéro_ligne: $item["numéro_ligne"],
			);

			$commentaires[$id] = $commentaire;
		}

		return $commentaires;
	}
}
