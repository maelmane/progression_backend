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

namespace progression\domaine\interacteur;

use progression\domaine\entité\{Avancement, Question};

class SauvegarderTentativeProgInt extends Interacteur
{
	public function sauvegarder($tentative, $question_uri, $username)
	{
		$dao_avancement = $this->_source->get_avancement_prog_dao();
        $avancement = $dao_avancement->get_avancement($username, $question_uri);

        if ($avancement == null){
            $avancement = new Avancement([$tentative], Question::ETAT_DEBUT, Question::TYPE_PROG);
        }
        
		$avancement->etat = $tentative->réussi ? Question::ETAT_REUSSI : Question::ETAT_NONREUSSI;

		$dao_avancement->save($avancement, $username, $question_uri);
        $dao_tentative=$this->_source->get_tentative_prog_dao();
        $dao_tentative->save($tentative, $username, $question_uri);
	}
}
