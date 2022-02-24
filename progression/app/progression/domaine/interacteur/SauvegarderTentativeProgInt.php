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
	public function sauvegarder($username, $question_uri, $tentative)
	{
		$dao_avancement = $this->source_dao->get_avancement_dao();
		$avancement = $dao_avancement->get_avancement($username, $question_uri);

		if ($avancement == null) {
			$avancement = new Avancement(
				$tentative->réussi ? Question::ETAT_REUSSI : Question::ETAT_NONREUSSI,
				Question::TYPE_PROG,
				[$tentative],
				[],
			);
			$dao_avancement->save($username, $question_uri, $avancement);
		} else {
			$date = (new \DateTime())->getTimestamp();
			if($avancement->etat != Question::ETAT_REUSSI && $tentative->réussi){
				
				$avancement->etat = Question::ETAT_REUSSI;
				$avancement->tentatives[] = $tentative;
				$avancement->date_réussite = $date;
			}
			

			$avancement->date_modification = $date;
			$dao_avancement->save($username, $question_uri, $avancement);
		}
			
		
		
		$dao_tentative = $this->source_dao->get_tentative_prog_dao();
		return $dao_tentative->save($username, $question_uri, $tentative);
	}


}
