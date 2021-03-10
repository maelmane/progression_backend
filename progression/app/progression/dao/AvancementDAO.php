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

use progression\domaine\entité\{Avancement, AvancementProg, AvancementSys, AvancementBD, Question};

class AvancementDAO extends EntitéDAO
{
	public function get_avancement($question_uri, $username)
	{
		$type = (new QuestionDAO())->get_type($question_uri);
		$avancement = null;

		if ($type == null) {
			return null;
		} else {
			if ($type == Question::TYPE_PROG) {
				$avancement = new AvancementProg($question_uri, $username);
				return (new AvancementProgDAO())->load($avancement);
			} elseif ($type == Question::TYPE_SYS) {
				$avancement = new AvancementSys($question_uri, $username);
				return (new AvancementSysDAO())->load($avancement);
			} elseif ($type == Question::TYPE_BD) {
				$avancement = new AvancementBD($question_uri, $username);
				return (new AvancementBDDAO())->load($avancement);
			}

			return $avancement;
		}
	}

	protected function load($objet)
	{
		$query = $this->conn->prepare("SELECT username, etat FROM avancement WHERE question_uri = ? AND username = ?");
		$query->bind_param("ii", $objet->question_uri, $objet->username);
		$query->execute();
		$query->bind_result($objet->id, $objet->etat);
		$query->fetch();

		$query->close();
	}
}
?>
