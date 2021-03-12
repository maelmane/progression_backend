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

use progression\domaine\entité\TentativeProg;

class TentativeProgDAO extends TentativeDAO
{
    public function get_tentative($username, $question_uri, $timestamp)
    {
        $tentative = null;

        $query = $this->conn->prepare(
            'SELECT reponse_prog.langage,
				reponse_prog.code,
				reponse_prog.date_soumission,
                reponse_prog.reussi
			 FROM reponse_prog
			 WHERE username = ? 
			 	AND question_uri = ?
			 	AND date_soumission = ?'
        );
        $query->bind_param("ssi", $username, $question_uri, $timestamp);
        $query->execute();

        $langage = null;
        $code = null;
        $date_soumission = null;
        $réussi = null;
        $query->bind_result($langage, $code, $date_soumission, $réussi);

        if ($query->fetch()) {
            $tentative = new TentativeProg(
                $langage,
                $code,
                $date_soumission,
                0,
                $réussi
            );
        }

        $query->close();

        return $tentative;
    }
}
