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

use progression\domaine\entité\{Tentative, TentativeProg, TentativeSys, TentativeBD, Question};

class TentativeDAO extends EntitéDAO
{
	public function get_tentative($username, $question_uri, $date)
	{
		$type = $this->get_type($username, $question_uri);

		if ($type == Question::TYPE_PROG) {
			return $this->_source->get_tentative_prog_dao()->get_tentative($username, $question_uri, $date);
		} elseif ($type == Question::TYPE_SYS) {
			return $this->_source->get_tentative_sys_dao()->get_tentative($username, $question_uri, $date);
		} elseif ($type == Question::TYPE_BD) {
			return $this->_source->get_tentative_bd_dao()->get_tentative($username, $question_uri, $date);
		} else {
			return null;
		}
	}

	private function get_type($username, $question_uri)
	{
		$type = null;

		$query = EntitéDAO::get_connexion()->prepare(
			"SELECT type FROM avancement WHERE question_uri = ? AND username = ?",
		);
		$query->bind_param("ss", $question_uri, $username);
		$query->execute();
		$query->bind_result($type);
		$query->fetch();
		$query->close();

		return $type;
	}
}
