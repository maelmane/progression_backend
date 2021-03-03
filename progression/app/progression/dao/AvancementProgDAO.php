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
	public function load($objet)
	{
		$query = $this->conn->prepare(
			'SELECT avancement.userID, avancement.questionID, etat, code, lang, lang_derniere_reponse, date_soumission
             FROM avancement 
             LEFT JOIN avancement_prog 
             ON avancement.questionID = avancement_prog.questionID AND
                avancement.userID = avancement_prog.userID
             LEFT JOIN reponse_prog 
             ON avancement.questionID = reponse_prog.questionID AND
                avancement.userID = reponse_prog.userID
             WHERE avancement.questionID = ? AND avancement.userID = ?'
		);
		$query->bind_param("ii", $objet->question_id, $objet->user_id);
		$query->execute();
		$query->bind_result(
			$objet->user_id,
			$objet->question_id,
			$objet->etat,
			$code,
			$lang,
			$objet->lang,
			$date
		);

		$objet->user_id = null;
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
				->prepare('INSERT INTO avancement ( etat, questionID, userID, type ) VALUES ( ?, ?, ? )
                                              ON DUPLICATE KEY UPDATE etat = VALUES( etat ) ');

			$query->bind_param(
				"iii",
				$objet->etat,
				$objet->question_id,
				$objet->user_id,
				Question::TYPE_PROG
			);
			$query->execute();
			$query->close();

			$query = $this->conn
				->prepare('INSERT INTO avancement_prog ( questionID, userID, lang_derniere_reponse ) VALUES ( ?, ?, ? )
                                              ON DUPLICATE KEY UPDATE lang_derniere_reponse = VALUES( lang_derniere_reponse ) ');

			$query->bind_param(
				"iii",
				$objet->question_id,
				$objet->user_id,
				$objet->lang
			);
			$query->execute();
			$query->close();

			$query = $this->conn
				->prepare('INSERT INTO reponse_prog ( questionID, userID, lang, code ) VALUES ( ?, ?, ?, ?  )
                                              ON DUPLICATE KEY UPDATE code=VALUES( code )');
			foreach ($objet->réponses as $réponse) {
				$query->bind_param(
					"iiis",
					$objet->question_id,
					$objet->user_id,
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
		return $this->get_avancement($objet->question_id, $objet->user_id);
	}
}
