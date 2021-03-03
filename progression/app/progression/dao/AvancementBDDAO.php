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

use progression\domaine\entité\{AvancementBD, Question};

class AvancementBDDAO extends EntitéDAO
{
	static function get_avancement($question_id, $username)
	{
		$avancement = new AvancementBD($question_id, $username);
		AvancementBDDAO::load($avancement);
		if (is_null($avancement->etat)) {
			$avancement->etat = Question::ETAT_DEBUT;
		}

		return $avancement->id ? $avancement : null;
	}

	protected static function load($objet)
	{
		$query = AvancementBDDAO::$conn->prepare(
			'SELECT id, etat, reponse, code, conteneur FROM avancement WHERE question_uri = ? AND name = ?'
		);
		$query->bind_param("ii", $objet->question_uri, $objet->username);
		$query->execute();
		$query->bind_result(
            $objet->id,
			$objet->etat,
			$objet->reponse,
			$objet->code_utilisateur,
			$objet->conteneur
		);
		$query->fetch();

		$query->close();
	}

    public static function save($objet)
    {
        $mysql->begin_transaction();
        try {
            $query = AvancementBDDAO::$conn
                ->prepare('INSERT INTO avancement ( etat, question_uri, username ) VALUES ( ?, ?, ? )
                                              ON DUPLICATE KEY UPDATE etat = VALUES( etat ) ');

            $query->bind_param(
                "iii",
                $objet->etat,
                $objet->question_uri,
                $objet->username
            );
            $query->execute();
            $query->close();

            $query = AvancementBDDAO::$conn
                ->prepare('INSERT INTO reponse_sys ( question_uri, username, reponse, conteneur ) VALUES ( ?, ?, ?, ?  )
                                              ON DUPLICATE KEY UPDATE reponse=VALUES( reponse ), conteneur=VALUES( conteneur )');

            $query->bind_param(
                "iiss",
                $objet->question_uri,
                $objet->username,
                $objet->reponse,
                $objet->conteneur
            );
            $query->execute();
            $query->close();

            $query = AvancementBDDAO::$conn
                ->prepare('INSERT INTO reponse_prog ( question_uri, username, lang, code ) VALUES ( ?, ?, ?, ?  )
                                              ON DUPLICATE KEY UPDATE lang=VALUES( lang ), code=VALUES( code )');

            $query->bind_param(
                "iiss",
                $objet->question_uri,
                $objet->username,
                "mysql",
                $objet->code
            );
            $query->execute();

            $mysqli->commit();
        } catch (\mysqli_sql_exception $exception) {
            $mysqli->rollback();

            throw $exception;
        }
        return AvancementBDDAO::get_avancement(
            $objet->question_uri,
            $objet->username
        );
    }
}
?>
