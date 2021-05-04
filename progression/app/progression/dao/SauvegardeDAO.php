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
	public function get_sauvegarde($username, $question_uri, $langage)
	{
		$sauvegarde = null;
		try{
			$query = EntitéDAO::get_connexion()->prepare(
				'SELECT sauvegarde.username,
					sauvegarde.question_uri,
					sauvegarde.date_sauvegarde,
					sauvegarde.langage,
					sauvegarde.code
				FROM sauvegarde
				WHERE username = ? 
				AND question_uri = ?
				AND langage = ?',
			);
			$query->bind_param("sss", $username, $question_uri, $langage);
			$query->execute();

			$langage = null;
			$code = null;
			$date_sauvegarde = null;
			$question_uri = null;
			$username = null;
			$query->bind_result($username, $question_uri, $date_sauvegarde, $langage, $code);

			if ($query->fetch()) {
				$sauvegarde = new Sauvegarde($username, $question_uri, $date_sauvegarde, $langage, $code);
			}

			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $sauvegarde;
	}

	public function save($sauvegarde)
	{
		try{
			$query = EntitéDAO::get_connexion()->prepare(
				"INSERT INTO sauvegarde ( username, question_uri, date_sauvegarde, langage, code )
				VALUES ( ?, ?, ?, ?, ? )
				ON DUPLICATE KEY UPDATE code = VALUES( code ), date_sauvegarde = VALUES( date_sauvegarde )",
			);
			$query->bind_param(
				"ssiss",
				$sauvegarde->username,
				$sauvegarde->question_uri,
				$sauvegarde->date_sauvegarde,
				$sauvegarde->langage,
				$sauvegarde->code
			);
			$query->execute();
			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $this->get_sauvegarde($sauvegarde->username, $sauvegarde->question_uri, $sauvegarde->langage);
	}
}
