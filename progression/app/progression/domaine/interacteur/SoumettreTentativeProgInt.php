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

class SoumettreTentativeProgInt extends Interacteur
{
	public function soumettre_tentative($username, $question, $tentative)
	{
		$exécutable = null;

		$préparerProgInt = new PréparerProgInt();
		$exécutable = $préparerProgInt->préparer_exécutable($question, $tentative);

		if ($exécutable) {
			$exécuterProgInt = new ExécuterProgInt();
			$tentative->résultats = $exécuterProgInt->exécuter($exécutable, $question->tests);

			$traiterTentativeProgInt = new TraiterTentativeProgInt();
			$tentativeTraité = $traiterTentativeProgInt->traiter_résultats($question, $tentative);

			/** */
			
			/** */
            $avancement = $this->récupérerAvancement($username, $question->uri, $tentativeTraité);
            $this->sauvegarderAvancement($username, $question->uri, $avancement);

			/** */
			$interacteurSauvegarde = new SauvegarderTentativeProgInt();
			$interacteurSauvegarde->sauvegarder($username, $question->uri, $tentativeTraité);
			/** */
			return $tentativeTraité;
		}
		return null;
	}

    private function récupérerAvancement($username, $uriQuestion, $tentative) {
        $dao_avancement = $this->source_dao->get_avancement_dao();
        $avancement = $dao_avancement->get_avancement($username, $uriQuestion);

        if ($avancement == null) {
            $avancement = $this->créerAvancement($tentative);
        }
		$avancement->tentatives[] = $tentative;
		
        return $avancement;
    }

    private function créerAvancement($tentative) {
        return new Avancement(
            $tentative->réussi ? Question::ETAT_REUSSI : Question::ETAT_NONREUSSI,
            Question::TYPE_PROG,
            [$tentative],
            []
        );
    }

    private function sauvegarderAvancement($username, $uriQuestion, $avancement) {
        $interacteurAvancement = new SauvegarderAvancementInt();
        $interacteurAvancement->sauvegarder($username, $uriQuestion, $avancement);
    }
}
