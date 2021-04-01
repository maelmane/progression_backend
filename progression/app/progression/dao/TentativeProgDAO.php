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

use progression\domaine\entité\TentativeProg;

class TentativeProgDAO extends TentativeDAO
{
	public function get_toutes($username, $question_uri)
	{
		$tentatives = [];

		$query = EntitéDAO::get_connexion()->prepare(
			'SELECT reponse_prog.langage,
				reponse_prog.code,
				reponse_prog.date_soumission,
                reponse_prog.reussi
			 FROM reponse_prog
			 WHERE username = ? 
			 	AND question_uri = ?',
		);
		$query->bind_param("ss", $username, $question_uri);
		$query->execute();

		$langage = null;
		$code = null;
		$date_soumission = null;
		$réussi = null;
		$query->bind_result($langage, $code, $date_soumission, $réussi);

		while ($query->fetch()) {
			$tentatives[] = new TentativeProg($langage, $code, $date_soumission, $réussi);
		}

		$query->close();

		return $tentatives;
	}

	public function get_tentative($username, $question_uri, $timestamp)
	{
		$tentative = null;

		$query = EntitéDAO::get_connexion()->prepare(
			'SELECT reponse_prog.langage,
				reponse_prog.code,
				reponse_prog.date_soumission,
                reponse_prog.reussi
			 FROM reponse_prog
			 WHERE username = ? 
			 	AND question_uri = ?
			 	AND date_soumission = ?',
		);
		$query->bind_param("ssi", $username, $question_uri, $timestamp);
		$query->execute();

		$langage = null;
		$code = null;
		$date_soumission = null;
		$réussi = null;
		$query->bind_result($langage, $code, $date_soumission, $réussi);

		if ($query->fetch()) {
			$tentative = new TentativeProg($langage, $code, $date_soumission, 0, $réussi);
		}

		$query->close();

		return $tentative;
	}

	public function save($username, $question_uri, $objet)
	{
		$query = EntitéDAO::get_connexion()->prepare(
			"INSERT INTO reponse_prog ( question_uri, username, langage, code, date_soumission, reussi ) VALUES ( ?, ?, ?, ?, ?, ?  )",
		);
		$query->bind_param(
			"ssssii",
			$question_uri,
			$username,
			$objet->langage,
			$objet->code,
			$objet->date_soumission,
			$objet->réussi,
		);
		$query->execute();
		$query->close();

		return $this->get_tentative($username, $question_uri, $objet->date_soumission);
	}
}
