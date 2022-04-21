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
			$tentative->résultats = $this->exécuterProg($exécutable, $question->tests);
			$tentativeTraité = $this->traiterTentativeProg($question, $tentative);
			$avancement = $this->récupérerAvancement($username, $question, $tentativeTraité);

			// Mise à jour du titre, du niveau et des dates
			$question_de_avancement = $this->récupérer_informations_de_la_question($question->uri);
			$avancement->titre = $question_de_avancement->titre;
			$avancement->niveau = $question_de_avancement->niveau;
			$avancement = $this->mettreÀJourDateModificationEtDateRéussiePourAvancement($avancement);

			$this->sauvegarderAvancement($username, $question->uri, $avancement);

			$interacteurSauvegarde = new SauvegarderTentativeProgInt();
			$interacteurSauvegarde->sauvegarder($username, $question->uri, $tentativeTraité);

			return $tentativeTraité;
		}
		return null;
	}

	private function récupérerAvancement($username, $question, $tentative)
	{
		$dao_avancement = $this->source_dao->get_avancement_dao();
		$avancement = $dao_avancement->get_avancement($username, $question->uri);

		if ($avancement == null) {
			$avancement = $this->créerAvancement($tentative, $question);
		} else {
			$avancement->tentatives[] = $tentative;
		}

		return $avancement;
	}

	private function créerAvancement($tentative, $question)
	{
		$avancement = new Avancement(Question::ETAT_NONREUSSI, Question::TYPE_PROG, [$tentative], []);
		$avancement->titre = $question->titre;
		$avancement->niveau = $question->niveau;
		return $avancement;
	}

	private function mettreÀJourDateModificationEtDateRéussiePourAvancement($avancement)
	{
		$date = (new \DateTime())->getTimestamp();
		if (!empty($avancement->tentatives)) {
			$tentatives = $avancement->tentatives;
			$tentative = $tentatives[0];
			foreach ($tentatives as $t) {
				if ($t->réussi == Question::ETAT_REUSSI) {
					$tentative = $t;
					break;
				}
			}

			if ($avancement->etat != Question::ETAT_REUSSI && $tentative->réussi) {
				$avancement->etat = Question::ETAT_REUSSI;
				$avancement->date_réussite = $date;
			}
		}
		$avancement->date_modification = $date;
		return $avancement;
	}

	private function exécuterProg($exécutable, $testsQuestion)
	{
		$exécuterProgInt = new ExécuterProgInt();
		return $exécuterProgInt->exécuter($exécutable, $testsQuestion);
	}

	private function traiterTentativeProg($question, $tentative)
	{
		$traiterTentativeProgInt = new TraiterTentativeProgInt();
		return $traiterTentativeProgInt->traiter_résultats($question, $tentative);
	}

	private function sauvegarderAvancement($username, $uriQuestion, $avancement)
	{
		$interacteurAvancement = new SauvegarderAvancementInt();
		$interacteurAvancement->sauvegarder($username, $uriQuestion, $avancement);
	}

	private function récupérer_informations_de_la_question($question_uri)
	{
		$dao_question = $this->source_dao->get_question_dao();
		$question = $dao_question->get_question($question_uri);
		return $question;
	}
}
