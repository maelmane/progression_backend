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

use progression\domaine\entité\user\User;
use progression\dao\models\UserMdl;

use DB;
use Illuminate\Database\QueryException;

class UserDAO extends EntitéDAO
{
	public function get_user($username, $includes = [])
	{
		$user = null;

		try {
			$user = UserMdl::query()
				->where("username", $username)
				->with(in_array("avancements", $includes) ? "avancements" : [])
				->with(in_array("clés", $includes) ? "clés" : [])
				->first();
			return $user ? $this->construire([$user], $includes)[0] : null;
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function save($user)
	{
		try {
			$objet = [
				"username" => $user->username,
				"courriel" => $user->courriel,
				"état" => $user->état,
				"rôle" => $user->rôle,
				"preferences" => $user->préférences,
			];

			return $this->construire([UserMdl::query()->updateOrCreate(["username" => $user->username], $objet)])[0];
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function set_password(User $user, string $password)
	{
		try {
			$hash = password_hash($password, PASSWORD_DEFAULT);
			return DB::update("UPDATE user SET password=? WHERE username=?", [$hash, $user->username]);
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function vérifier_password(User $user, string $password)
	{
		try {
			$hash = DB::select("SELECT password FROM user WHERE username=?", [$user->username]);
			return count($hash) == 1 && password_verify($password, $hash[0]->password);
		} catch (QueryException $e) {
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
				$user["courriel"],
				$user["état"],
				$user["rôle"],
				in_array("avancements", $includes)
					? AvancementDAO::construire($user["avancements"], parent::filtrer_niveaux($includes, "avancements"))
					: [],
				in_array("clés", $includes)
					? CléDAO::construire($user["clés"], parent::filtrer_niveaux($includes, "clés"))
					: [],
				$user["preferences"] ?? "",
			);
		}
		return $users;
	}
}
