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

use progression\domaine\entité\Avancement;
use progression\dao\DAOException;

class ObtenirAvancementInt extends Interacteur
{
	/**
	 * @param mixed $includes
	 * liste des sous-objets à inclure; true pour inclure tous les niveaux.
	 */
	function get_avancement($username, $question_uri, mixed $includes = []): Avancement|null
	{
		try {
			$avancement = $this->source_dao->get_avancement_dao()->get_avancement($username, $question_uri, $includes);
		} catch (DAOException $e) {
			throw new IntéracteurException($e, 503);
		}

		return $avancement;
	}
}
