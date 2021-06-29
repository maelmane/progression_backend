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
	public function get_clé($username, $numéro)
	{
		$clé = null;

		$création = null;
		$expiration = null;
		$portée = null;

		try {
			$query = EntitéDAO::get_connexion()->prepare(
				"SELECT creation, expiration, portee FROM cle WHERE username = ? AND numero = ? ",
			);
			$query->bind_param("ss", $username, $numéro);

			$query->execute();
			$query->bind_result($création, $expiration, $portée);

			$résultat = $query->fetch();
			$query->close();
			if ($résultat) {
				$clé = new Clé($numéro, $création, $expiration, $portée);
			}
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $clé;
	}

	public function save($username, $numéro, $objet)
	{
		try {
			$query = EntitéDAO::get_connexion()->prepare(
				"INSERT INTO cle ( username, numero, creation, expiration, portee ) VALUES ( ?, ?, ?, ?, ? )",
			);

			$query->bind_param("ssiii", $username, $numéro, $objet->création, $objet->expiration, $objet->portée);
			$query->execute();
			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $this->get_clé($username, $numéro);
	}
}
