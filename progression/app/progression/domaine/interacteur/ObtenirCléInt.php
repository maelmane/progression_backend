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

use progression\domaine\entité\clé\Clé;
use progression\dao\DAOException;

class ObtenirCléInt extends Interacteur
{
	/**
	 * @param mixed $includes
	 * liste des sous-objets à inclure; true pour inclure tous les niveaux.
	 */
	public function get_clé($username, $numéro, mixed $includes = []): Clé|null
	{
		try {
			$clé = $this->source_dao->get_clé_dao()->get_clé($username, $numéro, $includes);
		} catch (DAOException $e) {
			throw new IntéracteurException($e, 503);
		}

		if ($clé != null) {
			//Caviarde le secret
			$clé->secret = null;
		}

		return $clé;
	}
}
