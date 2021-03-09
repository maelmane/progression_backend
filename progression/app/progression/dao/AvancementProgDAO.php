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

use progression\domaine\entité\{Question, TentativeProg};

class AvancementProgDAO extends EntitéDAO
{
	public function get_tentative($username, $question_uri, $timestamp)
	{
		$tentative = null;
		$query = $this->conn->prepare(
			'SELECT lang, code, date_soumission
             FROM reponse_prog
             WHERE user_id = ? AND question_uri = ?
             AND date_soumission = ?',
		);
		$query->bind_param("iii", $username, $question_uri, $timestamp);
		$query->execute();
		$query->bind_result($lang, $code, $date_soumission);

		if ($lang && $code) {
			$tentative = new TentativeProg($lang, $code, $date_soumission);
		}
		$query->close();

		return $tentative;
	}

	protected function load($objet)
	{
		parent::load($objet);

		$query = $this->conn->prepare(
			'SELECT avancement.user_id, avancement.question_uri, etat, code, langage, date_soumission
             FROM avancement 
             LEFT JOIN reponse_prog 
             ON avancement.question_uri = reponse_prog.question_uri AND
                avancement.username = reponse_prog.username
             WHERE avancement.question_uri = ? AND avancement.username = ?'
		);
		$query->bind_param("ii", $objet->question_uri, $objet->username);
		$query->execute();
		$query->bind_result(
			$objet->username,
			$objet->question_uri,
			$objet->etat,
			$code,
			$lang,
			$date
		);

		$objet->username = null;
		$réponses = [];
		while ($query->fetch()) {
			$réponses[$lang] = new TentativeProg($lang, $code, $date);
		}

		$objet->réponses = $réponses;
		$query->close();
	}

	public function save($objet)
	{
		$this->conn->begin_transaction();
		try {
			$query = $this->conn
				->prepare('INSERT INTO avancement ( etat, question_uri, username, type ) VALUES ( ?, ?, ? )
                                              ON DUPLICATE KEY UPDATE etat = VALUES( etat ) ');

			$query->bind_param(
				"iii",
				$objet->etat,
				$objet->question_uri,
				$objet->username,
				Question::TYPE_PROG
			);
			$query->execute();
			$query->close();

			$query = $this->conn
				->prepare('INSERT INTO reponse_prog ( question_uri, username, lang, code ) VALUES ( ?, ?, ?, ?  )
                                              ON DUPLICATE KEY UPDATE code=VALUES( code )');
			foreach ($objet->réponses as $réponse) {
				$query->bind_param(
					"iiis",
					$objet->question_uri,
					$objet->username,
					$réponse->langid,
					$réponse->code
				);
				$query->execute();
			}
			$query->close();

			$this->conn->commit();
		} catch (\mysqli_sql_exception $exception) {
			$this->conn->rollback();

			throw $exception;
		}
		return $this->get_avancement($objet->question_uri, $objet->username);
	}
}
