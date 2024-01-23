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

use progression\domaine\entité\user\User;
use progression\dao\DAOException;

class ObtenirUserInt extends Interacteur
{
	/**
	 * @param mixed $includes
	 * lliste de niveaux de sous-objets à inclure; true pour inclure tous les niveaux.
	 */
	function get_user(string $username = null, mixed $includes = []): User|null
	{
		$dao = $this->source_dao->get_user_dao();
		$user = $dao->get_user(username: $username, includes: $includes);

		return $user;
	}

	/**
	 * @param mixed $includes
	 * lliste de niveaux de sous-objets à inclure; true pour inclure tous les niveaux.
	 */
	function trouver(string $username = null, string $courriel = null, mixed $includes = []): User|null
	{
		try {
			$dao = $this->source_dao->get_user_dao();
			$user = $dao->trouver(username: $username, courriel: $courriel, includes: $includes);
		} catch (DAOException $e) {
			throw new IntéracteurException($e, 503);
		}

		return $user;
	}
}
