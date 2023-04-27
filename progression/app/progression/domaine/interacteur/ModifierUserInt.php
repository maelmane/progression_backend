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

use progression\domaine\entité\User;
use progression\dao\DAOFactory;
use DomainException;

class ModifierUserInt extends Interacteur
{
	public function modifier_préférences(User $user, string $préférences): User
	{
		$user->préférences = $préférences;

		return $user;
	}
	public function modifier_état(User $user, int $état): User
	{
		if ($état == User::ÉTAT_ATTENTE_DE_VALIDATION) {
			throw new DomainException("Transition d'état invalide");
		}

		$user->état = $état;

		return $user;
	}
}
