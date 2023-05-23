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
use progression\dao\DAOException;

class SauvegarderCommentaireInt extends Interacteur
{
	public function sauvegarder_commentaire($username, $question_uri, $date_soummision, $numéro, $commentaire)
	{
		if (!$commentaire->message || !$commentaire->créateur) {
			return null;
		}

		try {
			return $this->source_dao
				->get_commentaire_dao()
				->save($username, $question_uri, $date_soummision, $numéro, $commentaire);
		} catch (DAOException $e) {
			throw new IntéracteurException($e);
		}
	}
}
