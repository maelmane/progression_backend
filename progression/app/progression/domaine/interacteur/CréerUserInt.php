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

class CréerUserInt extends Interacteur
{
	function créer_user($username, string $courriel = null)
	{
		if (!$username) {
			return null;
		}

		$user_dao = $this->source_dao->get_user_dao();

		try {
			$user = $user_dao->get_user($username);
			if ($user) {
				return null;
			} else {
				$user = new User($username, $courriel);
				$user = $user_dao->save($user);
			}
		} catch (DAOException $e) {
			throw new IntéracteurException($e, 503);
		}

		return $user;
	}
}
