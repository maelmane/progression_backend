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
use progression\domaine\entité\Clé;

class CléDAO extends EntitéDAO
{
	public function get_clé($username, $nom)
	{
		$clé = null;

		$secret = null;
		$création = null;
		$expiration = null;
		$portée = null;

		try {
			$query = EntitéDAO::get_connexion()->prepare(
				"SELECT hash, creation, expiration, portee FROM cle WHERE username = ? AND nom = ? ",
			);
			$query->bind_param("ss", $username, $nom);

			$query->execute();
			$query->bind_result($secret, $création, $expiration, $portée);

			$résultat = $query->fetch();
			$query->close();
			if ($résultat) {
				$clé = new Clé($secret, $création, $expiration, $portée);
			}
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $clé;
	}

	public function save($username, $nom, $objet)
	{
		try {
			$query = EntitéDAO::get_connexion()->prepare(
				"INSERT INTO cle ( username, nom, hash, creation, expiration, portee ) VALUES ( ?, ?, ?, ?, ?, ? )",
			);

			$hash = hash("sha256", $objet->secret);

			$query->bind_param("sssiii", $username, $nom, $hash, $objet->création, $objet->expiration, $objet->portée);
			$query->execute();
			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		$clé = $this->get_clé($username, $nom);
		$clé->secret = $objet->secret;

		return $clé;
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
}
