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
class ObtenirTentativeInt extends Interacteur
{
	function get_tentative($username, $question_uri, $date)
	{
		$tentative = $this->source_dao->get_tentative_dao()->get_tentative($username, $question_uri, $date);
		if ($tentative) {
			$tentative->commentaires = $this->source_dao
				->get_commentaire_dao()
				->get_commentaires_par_tentative($username, $question_uri, $date);
		}
		return $tentative;
	}

	function get_id_conteneur_dernière_tentative($username, $question_uri)
	{
		$tentatives = $this->source_dao->get_tentative_dao()->get_toutes($username, $question_uri);

		$tentative = $tentatives[count($tentatives) - 1];

		print_r($tentative);

		return $tentative->conteneur;
	}
}
