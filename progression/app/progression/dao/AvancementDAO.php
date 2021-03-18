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

use progression\domaine\entité\{Avancement, Question};

class AvancementDAO extends EntitéDAO
{
	public function get_avancement($username, $question_uri)
	{
		$avancement = $this->load($question_uri, $username);
		if ($avancement->type == Question::TYPE_PROG) {
			$avancement->tentatives = $this->_source->get_tentative_prog_dao()->get_toutes($username, $question_uri);
			return $avancement;
		} else {
			return null;
		}
	}

	protected function load($question_uri, $username)
	{
		$avancement = new Avancement();

		$query = EntitéDAO::get_connexion()->prepare(
			"SELECT etat, type FROM avancement WHERE question_uri = ? AND username = ?",
		);
		$query->bind_param("ss", $question_uri, $username);
		$query->execute();
		$query->bind_result($avancement->etat, $avancement->type);
		$query->fetch();

		$query->close();
		return $avancement;
	}

	public function save($question_uri, $username, $objet)
	{
		$query = EntitéDAO::get_connexion()
			->prepare('INSERT INTO avancement ( etat, question_uri, username, type ) VALUES ( ?, ?, ? )
                                              ON DUPLICATE KEY UPDATE etat = VALUES( etat ) ');

		$query->bind_param("iss", $objet->etat, $question_uri, $username, Question::TYPE_PROG);
		$query->execute();
		$query->close();

		return $this->get_avancement($objet->question_uri, $objet->username);
	}
}
