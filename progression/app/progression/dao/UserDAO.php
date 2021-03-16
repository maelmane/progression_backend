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

namespace progression\dao;

use progression\domaine\entité\User;

class UserDAO
{
	public function get_user($username)
	{
		$objet = new User($username);

		$query = EntitéDAO::get_connexion()->prepare("SELECT username, role FROM user WHERE username = ? ");
		$query->bind_param("s", $objet->username);

		$query->execute();

		$query->bind_result($objet->username, $objet->rôle);

		$résultat = $query->fetch();
		$query->close();

		return $résultat != null ? $objet : null;
	}

	public function save($objet)
	{
		$query = EntitéDAO::get_connexion()->prepare(
			"INSERT INTO user( username, role ) VALUES ( ?, ? ) ON DUPLICATE KEY UPDATE role=VALUES( role )",
		);
		$query->bind_param("si", $objet->username, $objet->rôle);
		$query->execute();
		$query->close();

		return $this->get_user($objet->username);
	}
}
