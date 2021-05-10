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
use progression\domaine\entité\Sauvegarde;

class SauvegardeDAO extends EntitéDAO
{
	public function get_toutes($username, $question_uri)
	{
		$sauvegardes = [];

		try {
			$query = EntitéDAO::get_connexion()->prepare(
				"SELECT date_sauvegarde, langage, code type FROM sauvegarde WHERE username = ? AND question_uri = ?"
			);
			$query->bind_param("ss", $username, $question_uri);
			$query->execute();

			$date_sauvegarde = null;
			$langage = null;
			$code = null;
			$query->bind_result($date_sauvegarde, $langage, $code);
			while ($query->fetch()) {
				$sauvegardes[$langage] = new Sauvegarde($date_sauvegarde, $code);
			}

			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $sauvegardes;
	}

	public function get_sauvegarde($username, $question_uri, $langage)
	{
		$sauvegarde = null;
		try {
			$query = EntitéDAO::get_connexion()->prepare(
				'SELECT
					sauvegarde.date_sauvegarde,
					sauvegarde.code
				FROM sauvegarde
				WHERE username = ? 
				AND question_uri = ?
				AND langage = ?',
			);
			$query->bind_param("sss", $username, $question_uri, $langage);
			$query->execute();

			$code = null;
			$date_sauvegarde = null;
			$query->bind_result($date_sauvegarde, $code);

			if ($query->fetch()) {
				$sauvegarde = new Sauvegarde($date_sauvegarde, $code);
			}

			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $sauvegarde;
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
				$sauvegarde->code
			);
			$query->execute();
			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $this->get_sauvegarde($username, $question_uri, $langage);
	}
}
