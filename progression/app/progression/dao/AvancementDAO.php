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
	static function get_avancement($question_id, $user_id)
	{
		$avancement = new Avancement($question_id, $user_id);
		AvancementDAO::load($avancement);
		if (is_null($avancement->etat)) {
			$avancement->etat = Question::ETAT_DEBUT;
		}

		return $avancement;
	}

	protected static function load($objet)
	{
		$query = AvancementDAO::$conn->prepare(
			'SELECT etat FROM avancement WHERE questionID = ? AND userID = ?'
		);
		$query->bind_param("ii", $objet->question_id, $objet->user_id);
		$query->execute();
		$query->bind_result(
			$objet->etat
		);
		$query->fetch();

		$query->close();
	}

	public static function save($objet)
	{
		$query = AvancementDAO::$conn
			->prepare('INSERT INTO avancement ( etat, questionID, userID ) VALUES ( ?, ?, ?, ?, ?, ? )
                                              ON DUPLICATE KEY UPDATE etat = VALUES( etat ) ');

		$query->bind_param(
			"iiisss",
			$objet->etat,
			$objet->question_id,
			$objet->user_id
		);
		$query->execute();
		$query->close();

		return AvancementDAO::get_avancement(
			$objet->question_id,
			$objet->user_id
		);
	}
}
?>
