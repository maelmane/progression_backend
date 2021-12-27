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

use mysqli_sql_exception;
use progression\domaine\entité\User;

class UserDAO extends EntitéDAO
{
	public function get_user($username)
	{
		$objet = new User($username);

		try {
			$query = EntitéDAO::get_connexion()->prepare("SELECT username, role FROM user WHERE username = ? ");
			$query->bind_param("s", $objet->username);

			$query->execute();

			$query->bind_result($objet->username, $objet->rôle);

			$résultat = $query->fetch();
			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		if ($résultat != null) {
			$objet->avancements = $this->source->get_avancement_dao()->get_tous($username);
			$objet->clés = $this->source->get_clé_dao()->get_toutes($username);
		}

		return $résultat != null ? $objet : null;
	}

	public function save($objet)
	{
		try {
			$query = EntitéDAO::get_connexion()->prepare(
				"INSERT INTO user( username, role ) VALUES ( ?, ? ) ON DUPLICATE KEY UPDATE role=VALUES( role )",
			);
			$query->bind_param("si", $objet->username, $objet->rôle);
			$query->execute();
			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $this->get_user($objet->username);
	}

	public function set_password(User $user, string $password)
	{
		try {
			$query = EntitéDAO::get_connexion()->prepare("UPDATE user SET password=? WHERE username=?");

			$hash = password_hash($password, PASSWORD_DEFAULT);
			$query->bind_param("ss", $hash, $user->username);
			$query->execute();
			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}
	}

	public function vérifier_password(User $user, string $password = null)
	{
		try {
			$query = EntitéDAO::get_connexion()->prepare("SELECT password FROM user WHERE username=?");

			$query->bind_param("s", $user->username);
			$query->execute();

			$hash = null;
			$query->bind_result($hash);
			$query->fetch();
			$query->close();

			return $hash && $password && password_verify($password, $hash);
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}
	}
}
