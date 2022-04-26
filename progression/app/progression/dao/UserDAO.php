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
use progression\domaine\entité\{User, Avancement, Clé};

class UserDAO extends EntitéDAO
{
	const QUERY_SELECT = "user.username, user.role ";
	const QUERY_FROM = " user "; 

	public function get_user($username, $includes=[])
	{
		try {
			$query_select = UserDAO::QUERY_SELECT;
			$query_from = UserDAO::QUERY_FROM;
			
			if (in_array("avancements", $includes)){
				$query_select .= ", " . AvancementDAO::QUERY_SELECT;
				$query_from .= " " . AvancementDAO::QUERY_FROM;
			}
			if (in_array("clés", $includes)){
				$query_select .= ", " . CléDAO::QUERY_SELECT;
				$query_from .= " " . CléDAO::QUERY_FROM;
			}

			$query_where = "WHERE user.username = ? ";
			$query = EntitéDAO::get_connexion()->prepare("SELECT " . $query_select . "FROM " . $query_from . $query_where);
			$query->bind_param("s", $username);

			$query->execute();

			$row = array();
			EntitéDAO::stmt_bind_assoc( $query, $row );

			$user = null;
			while( $query->fetch() ){
				if (!$user) $user = new User( $row["username"], $row["role"] );
				
				if (in_array("avancements", $includes)){
					if (!in_array( $row["question_uri"], $user->avancements ) ) {
						$user->avancements[ $row["question_uri"] ] = AvancementDAO::construire_avancement( $row );
					}
				}
				if (in_array("clés", $includes)){
					if (!in_array( $row["nom"], $user->clés ) ) {
						$user->clés[ $row["nom"] ] = CléDAO::construire_clé( $row );
					}
				}
			}
			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $user;
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
