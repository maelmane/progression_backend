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

use progression\domaine\entité\Commentaire;
use progression\dao\DAOFactory;

class ObtenirCommentaireInt extends Interacteur
{
	public function get_commentaire_par_id($id)
	{
		$commentaire = $this->source_dao->get_commentaire_dao()->get_commentaire($id);
		return $commentaire;
	}
	public function get_commentaires_par_tentative($username, $question_uri, $date)
	{
		$commentaires = $this->source_dao
			->get_commentaire_dao()
			->get_commentaires_par_tentative($username, $question_uri, $date);
		return $commentaires;
	}
}
