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
use progression\dao\models\UserMdl;
use Illuminate\Database\QueryException;

class UserDAO extends EntitéDAO
{
	public function get_user($username, $includes = [])
	{
		$user = null;

		try {
			$user = UserMdl::query()
				->where("username", $username)
				->with("avancements", "clés")
				->first();
			return $user ? $this->construire([$user], $includes)[0] : null;
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function save($user)
	{
		try {
			$objet = [];
			$objet["username"] = $user->username;
			$objet["role"] = $user->rôle;

			return $this->construire([UserMdl::query()->updateOrCreate(["username" => $user->username], $objet)])[0];
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
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

	public static function construire($data, $includes = [])
	{
		if ($data === null || count($data) == 0) {
			return null;
		}

		$users = [];
		foreach ($data as $user) {
			$users[] = new User(
				$user["username"],
				$user["role"],
				in_array("avancements", $includes) ? AvancementDAO::construire($user["avancements"]) : [],
				in_array("clés", $includes) ? CléDAO::construire($user["clés"]) : [],
			);
		}
		return $users;
	}
}
