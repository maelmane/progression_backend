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

class AvancementProgDAO extends AvancementDAO
{
    protected function load($objet)
    {
        parent::load($objet);

        $code = null;
        $lang = null;
        $date = null;

        $query = $this->conn->prepare(
            'SELECT avancement.username, 
				avancement.question_uri, 
				avancement.etat, 
				reponse_prog.code, 
				reponse_prog.langage, 
				reponse_prog.date_soumission
             FROM avancement 
             LEFT JOIN reponse_prog 
             ON avancement.question_uri = reponse_prog.question_uri 
			 	AND avancement.username = reponse_prog.username
             WHERE avancement.question_uri = ? 
			 	AND avancement.username = ?'
        );
        $query->bind_param("ss", $objet->question_uri, $objet->username);
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
        $résultats = [];
        while ($query->fetch()) {
            $résultats[$lang] = new TentativeProg($lang, $code, $date);
        }

        $objet->résultats = $résultats;
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
                "iss",
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
                    "ssis",
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
