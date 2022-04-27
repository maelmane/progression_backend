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
use progression\models\SauvegardeMdl;

class SauvegardeDAO extends EntitéDAO
{
	public function get_toutes($username, $question_uri)
	{
		$sauvegardes = [];

		try {
			$data = SauvegardeMdl::where("username", $username)
				->where("question_uri", $question_uri)
				->get();

			foreach ($data as $item) {
				$sauvegardes[$item->langage] = $this->construire($item);
			}
		} catch (QueryException $e) {
			throw new DAOException($e);
		}

		return $sauvegardes;
	}

	public function get_sauvegarde($username, $question_uri, $langage)
	{
		try {
			return $this->construire(
				SauvegardeMdl::where("username", $username)
					->where("question_uri", $question_uri)
					->where("langage", $langage)
					->first(),
			);
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	private function construire($data)
	{
		if ($data == null) {
			return null;
		}

		return new Sauvegarde($data->date_sauvegarde, $data->code);
	}

	public function save($username, $question_uri, $langage, $sauvegarde)
	{
		try {
			$query = EntitéDAO::get_connexion()->prepare(
				"INSERT INTO sauvegarde ( username, question_uri, date_sauvegarde, langage, code )
				VALUES ( ?, ?, ?, ?, ? )
				ON DUPLICATE KEY UPDATE code = VALUES( code ), date_sauvegarde = VALUES( date_sauvegarde )",
			);

			$query->bind_param(
				"ssiss",
				$username,
				$question_uri,
				$sauvegarde->date_sauvegarde,
				$langage,
				$sauvegarde->code,
			);
			$estEnregistre = $query->execute();
			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}
		return $sauvegarde;
	}
}
