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

use progression\domaine\entité\{AvancementSys, Question, RéponseSys};

class AvancementSysDAO extends EntitéDAO
{
    static function get_avancement($question_uri, $username)
    {
        $avancement = new AvancementSys($question_uri, $username, null, null);
        AvancementSysDAO::load($avancement);
        if (is_null($avancement->etat)) {
            $avancement->etat = Question::ETAT_DEBUT;
        }

		return $avancement->id ? $avancement : null;
    }

    protected static function load($objet)
    {
        $query = AvancementSysDAO::$conn->prepare(
            'SELECT id, etat, reponse, conteneur 
             FROM avancement LEFT JOIN reponse_sys 
             ON avancement.questionID = reponse_sys.questionID AND
                avancement.userID = reponse_sys.userID
             WHERE avancement.questionID = ? AND avancement.userID = ?'
        );
        $query->bind_param("ii", $objet->question_uri, $objet->username);
        $query->execute();
        $query->bind_result($objet->id, $objet->etat, $objet->reponse, $objet->conteneur);
        $query->fetch();

        $query->close();
    }

    public static function save($objet)
    {
        AvancementSysDAO::$conn->begin_transaction();
        try {
            $query = AvancementSysDAO::$conn
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

            $query = AvancementSysDAO::$conn
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

            AvancementProgDAO::$conn->commit();
        } catch (\mysqli_sql_exception $exception) {
            AvancementProgDAO::$conn->rollback();

            throw $exception;
        }
        return AvancementSysDAO::get_avancement(
            $objet->question_uri,
            $objet->username
        );
    }
}
?>
