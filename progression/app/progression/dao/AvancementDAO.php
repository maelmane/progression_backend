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

use progression\domaine\entité\{
	AvancementProg,
	AvancementSys,
	AvancementBD,
	Question,
	TentativeProg
};

class AvancementDAO extends EntitéDAO
{
	public function get_avancement($user_id, $question_id)
	{
		$type = (new QuestionDAO())->get_type($question_id);
		$avancement = null;

		if ($type == null) {
			return null;
		} else {
			if ($type == Question::TYPE_PROG) {
				$avancement = new AvancementProg($question_id, $user_id);
				(new AvancementProgDAO())->load($avancement);
			} elseif ($type == Question::TYPE_SYS) {
				$avancement = new AvancementSys($question_id, $user_id);
				(new AvancementSysDAO())->load($avancement);
			} elseif ($type == Question::TYPE_BD) {
				$avancement = new AvancementBD($question_id, $user_id);
				(new AvancementBDDAO())->load($avancement);
			}

			return $avancement;
		}
	}

	public function get_tentative($userid, $questionid, $timestamp)
	{
		$tentative = null;
		$query = $this->conn->prepare(
			'SELECT lang, code, date_soumission
             FROM reponse_prog
             WHERE userID = ? AND questionID = ?
             AND date_soumission = ?'
		);
		$query->bind_param("iii", $userid, $questionid, $timestamp);
		$query->execute();
		$query->bind_result(
			$lang,
			$code,
			$date_soumission
		);

		if (is_null($query->fetch())) {
			error_log($query->error);
		}

		if ($lang && $code) {
			$tentative = new TentativeProg($lang, $code, $date_soumission);
		}
		$query->close();

		return $tentative;
	}

	protected function load($objet)
	{
		$query = $this->conn->prepare(
			"SELECT userID, etat FROM avancement WHERE questionID = ? AND userID = ?"
		);
		$query->bind_param("ii", $objet->question_id, $objet->user_id);
		$query->execute();
		$query->bind_result($objet->id, $objet->etat);
		$query->fetch();

		$query->close();
	}
}
