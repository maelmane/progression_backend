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
use progression\domaine\entité\Sauvegarde;
use progression\dao\models\{AvancementMdl, SauvegardeMdl};
use progression\domaine\interacteur\IntégritéException;

class SauvegardeDAO extends EntitéDAO
{
	/**
	 * @return array<Sauvegarde>
	 */
	public function get_toutes($username, $question_uri, $includes = []): array
	{
		try {
			return $this->construire(
				SauvegardeMdl::select("sauvegarde.*")
					->join("avancement", "sauvegarde.avancement_id", "=", "avancement.id")
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

	public function get_sauvegarde($username, $question_uri, $langage, $includes = []): Sauvegarde|null
	{
		try {
			$sauvegarde = SauvegardeMdl::select("sauvegarde.*")
				->join("avancement", "sauvegarde.avancement_id", "=", "avancement.id")
				->join("user", "avancement.user_id", "=", "user.id")
				->where("user.username", $username)
				->where("avancement.question_uri", $question_uri)
				->where("langage", $langage)
				->first();

			return self::premier_élément($this->construire([$sauvegarde], $includes));
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	/**
	 * @return array<Sauvegarde>
	 */
	public function save($username, $question_uri, $langage, $sauvegarde): array
	{
		try {
			$avancement = AvancementMdl::select("avancement.id")
				->from("avancement")
				->join("user", "avancement.user_id", "=", "user.id")
				->where("user.username", $username)
				->where("question_uri", $question_uri)
				->first();

			if (!$avancement) {
				throw new IntégritéException("Impossible de sauvegarder la ressource; le parent n'existe pas.");
			}

			$objet = [
				"date_sauvegarde" => $sauvegarde->date_sauvegarde,
				"langage" => $langage,
				"code" => $sauvegarde->code,
			];

			return $this->construire([
				SauvegardeMdl::updateOrCreate(
					[
						"avancement_id" => $avancement["id"],
						"langage" => $langage,
					],
					$objet,
				),
			]);
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public static function construire($data, $includes = [])
	{
		$sauvegardes = [];
		foreach ($data as $item) {
			if ($item == null) {
				continue;
			}
			$sauvegardes[$item["langage"]] = new Sauvegarde($item["date_sauvegarde"], $item["code"]);
		}

		return $sauvegardes;
	}
}
