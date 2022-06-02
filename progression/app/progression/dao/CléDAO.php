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

use Illuminate\Database\QueryException;
use progression\domaine\entité\Clé;
use progression\dao\models\{CléMdl, UserMdl};

class CléDAO extends EntitéDAO
{
	public function get_clé($username, $nom, $includes = [])
	{
		try {
			$clé = 
				CléMdl::select("cle.*")
				->with( $includes )
                ->join("user", "user_id", "=", "user.id")
                ->where("user.username", $username)
                ->where("nom", $nom)
                ->first();

            if($clé){
                return $this->construire([$clé],
                                         $includes)[$nom];
			}
            else{
                return null;
            }            
		} catch (QueryException $e) {
			throw new DAOException($e);
		}

		return $clé;
	}

	public function get_toutes($username, $includes = [])
	{
        try{
            return $this->construire(
                 CléMdl::select("cle.*")
                 ->with( $includes )
                 ->join("user", "user_id", "=", "user.id")
                 ->where("user.username", $username)
                 ->get(),
                 $includes);
            
        } catch (QueryException $e) {
            throw new DAOException($e);
        }

	}

	public function save($username, $nom, $clé)
	{
        try{
            $user_id = UserMdl::select("user.id")
                         ->from("user")
                         ->where("user.username", $username)
                         ->first()["id"];
            $objet=[
                "user_id" => $user_id,
                "nom" => $nom,
                "hash" => hash("sha256", $clé->secret),
                "creation" => $clé->création,
                "expiration" => $clé->expiration,
                "portee" => $clé->portée
            ];
            return $this->construire([
                CléMdl::create($objet)])[$nom];
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function vérifier($username, $nom, $secret)
	{
		$hash = null;

		try {
			$query = EntitéDAO::get_connexion()->prepare("SELECT hash FROM cle WHERE username = ? AND nom = ? ");
			$query->bind_param("ss", $username, $nom);

			$query->execute();
			$query->bind_result($hash);

			$résultat = $query->fetch();
			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return hash("sha256", $secret) == $hash;
	}

	public static function construire($data)
	{
		$clés = [];
		foreach ($data as $item) {
            $nom = $item["nom"];
			$clés[$nom] = new Clé(null, $item["creation"], $item["expiration"], $item["portee"]);
		}
		return $clés;
	}
    
}
