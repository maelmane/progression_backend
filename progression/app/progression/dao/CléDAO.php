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

use DB;
use Illuminate\Database\QueryException;
use progression\domaine\entité\Clé;
use progression\dao\models\{CléMdl, UserMdl};

class CléDAO extends EntitéDAO
{
	public function get_clé($username, $nom, $includes = [])
	{
		try {
			$clé = CléMdl::select("cle.*")
				->join("user", "user_id", "=", "user.id")
				->where("user.username", $username)
				->where("nom", $nom)
				->first();

			return $clé ? $this->construire([$clé], $includes)[$nom] : null;
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function get_toutes($username, $includes = [])
	{
		try {
			return $this->construire(
				CléMdl::select("cle.*")
					->join("user", "user_id", "=", "user.id")
					->where("user.username", $username)
					->get(),
				$includes,
			);
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function save($username, $nom, $clé)
	{
		try {
			$user = UserMdl::select("user.id")
				->from("user")
				->where("user.username", $username)
				->first();

			if (!$user) {
				return null;
			}

			$secret_hashé = hash("sha256", $clé->secret);
			$objet = [
				"user_id" => $user["id"],
				"nom" => $nom,
				"hash" => $secret_hashé,
				"creation" => $clé->création,
				"expiration" => $clé->expiration,
				"portee" => $clé->portée,
			];
			$clé_créée = $this->construire([CléMdl::create($objet)])[$nom];

			// Le secret n'est pas stoqué directement dans la BD
			// On retourne la clé avec son secret en clair UNIQUEMENT ici.
			$clé_créée->secret = $clé->secret;

			return $clé_créée;
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function vérifier($username, $nom, $secret)
	{
		try {
			$hash = DB::select(
				"SELECT hash FROM cle JOIN user ON cle.user_id = user.id WHERE user.username = ? AND cle.nom = ? ",
				[$username, $nom],
			);

			return count($hash) == 1 && hash("sha256", $secret) == $hash[0]->hash;
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public static function construire($data, $includes = [])
	{
		$clés = [];
		foreach ($data as $item) {
			$nom = $item["nom"];
			$clés[$nom] = new Clé(null, $item["creation"], $item["expiration"], $item["portee"]);
		}
		return $clés;
	}
}
