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
			$résultats = $this->exécuter_prog($exécutable, $question->tests);
			$tentative->temps_exécution = $résultats["temps_exécution"];
			$tentative->résultats = $résultats["résultats"];
			$tentativeTraitée = $this->traiterTentativeProg($question, $tentative);
			$avancement = $this->récupérer_avancement($username, $question, $tentativeTraitée);

			$avancement->titre = $question->titre;
			$avancement->niveau = $question->niveau;
			$avancement = $this->mettre_à_jour_dates_et_état($avancement, $tentativeTraitée->date_soumission);

			$this->sauvegarder_avancement($username, $question->uri, $avancement);
			$this->sauvegarder_tentative($username, $question->uri, $tentativeTraitée);

			return $tentativeTraitée;
		}
		return null;
	}

	private function récupérer_avancement($username, $question, $tentative)
	{
		$dao_avancement = $this->source_dao->get_avancement_dao();
		$avancement = $dao_avancement->get_avancement($username, $question->uri);

		if ($avancement === null) {
			$avancement = $this->créer_avancement($question);
		}
		$avancement->tentatives[] = $tentative;
		return $avancement;
	}

	private function créer_avancement($question)
	{
		$avancement = new Avancement(Question::ETAT_NONREUSSI, Question::TYPE_PROG, []);
		return $avancement;
	}

	private function mettre_à_jour_dates_et_état($avancement, $date)
	{
		if (
			$avancement->etat != Question::ETAT_REUSSI &&
			$avancement->tentatives[array_key_last($avancement->tentatives)]->réussi
		) {
			$avancement->etat = Question::ETAT_REUSSI;
			$avancement->date_réussite = $date;
		}

		$avancement->date_modification = $date;
		return $avancement;
	}

	private function exécuter_prog($exécutable, $testsQuestion)
	{
		return (new ExécuterProgInt())->exécuter($exécutable, $testsQuestion);
	}

	private function traiterTentativeProg($question, $tentative)
	{
		return (new TraiterTentativeProgInt())->traiter_résultats($question, $tentative);
	}

	private function sauvegarder_avancement($username, $uriQuestion, $avancement)
	{
		(new SauvegarderAvancementInt())->sauvegarder($username, $uriQuestion, $avancement);
	}

	private function sauvegarder_tentative($username, $uriQuestion, $tentative)
	{
		$interacteurSauvegarde = new SauvegarderTentativeProgInt();
		$tentativeSauvegardée = $interacteurSauvegarde->sauvegarder($username, $uriQuestion, $tentative);
	}
}
