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

use progression\domaine\entité\{Avancement, Question, QuestionProg, QuestionSys, Tentative, User};

class SauvegarderAvancementInt extends Interacteur
{
	public function sauvegarder(string $username, string $question_uri, Avancement $nouvelAvancement): Avancement
	{
		$dao_avancement = $this->source_dao->get_avancement_dao();
		$avancement = $dao_avancement->save($username, $question_uri, $nouvelAvancement);
		return $avancement;
	}

	public function récupérer_avancement(string $username, string $question_uri, Tentative $tentative): Avancement
	{
		$dao_avancement = $this->source_dao->get_avancement_dao();
		$avancement = $dao_avancement->get_avancement($username, $question_uri);

		if ($avancement === null) {
			$avancement = new Avancement();
		}
		$avancement->tentatives[] = $tentative;
		return $avancement;
	}

	public function mettre_à_jour_dates_et_état(
		Avancement $avancement,
		int $date,
		string $username,
		string $question_uri
	): void {
		if (
			$avancement->etat != Question::ETAT_REUSSI &&
			$avancement->tentatives[array_key_last($avancement->tentatives)]->réussi
		) {
			$avancement->etat = Question::ETAT_REUSSI;
			$avancement->date_réussite = $date;
		}

		$avancement->date_modification = $date;
		$this->sauvegarder($username, $question_uri, $avancement);
	}
}
