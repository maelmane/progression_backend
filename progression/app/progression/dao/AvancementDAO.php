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
    public function get_avancement($question_id, $user_id)
    {
        $avancement = new Avancement($question_id, $user_id);
        $this->load($avancement);
        if (is_null($avancement->etat)) {
            $avancement->etat = Question::ETAT_DEBUT;
        }

        return $avancement->id ? $avancement : null;
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
?>
