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

use progression\domaine\entité\{Avancement, Question, QuestionProg, QuestionSys};

class SauvegarderAvancementInt extends Interacteur
{
	public function sauvegarder($username, $question_uri, $nouvelAvancement)
	{
		$dao_avancement = $this->source_dao->get_avancement_dao();
		$avancement = $dao_avancement->save($username, $question_uri, $nouvelAvancement);
		return $avancement;
	}

	public function récupérer_avancement($username, $question, $tentative)
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
		if ($question instanceof QuestionProg) {
			$avancement = new Avancement(Question::ETAT_NONREUSSI, Question::TYPE_PROG, []);
		} elseif ($question instanceof QuestionSys) {
			$avancement = new Avancement(Question::ETAT_NONREUSSI, Question::TYPE_SYS, []);
		} else {
			$avancement = new Avancement(Question::ETAT_NONREUSSI, Question::TYPE_BD, []);
		}

		return $avancement;
	}

	public function mettre_à_jour_dates_et_état($avancement, $date, $username, $question_uri)
	{
		if (
			$avancement->etat != Question::ETAT_REUSSI &&
			$avancement->tentatives[array_key_last($avancement->tentatives)]->réussi
		) {
			$avancement->etat = Question::ETAT_REUSSI;
			$avancement->date_réussite = $date;
		}

		$avancement->date_modification = $date;
	}
}
