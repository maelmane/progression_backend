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

namespace progression\dao\tentative;

use mysqli_sql_exception;
use progression\dao\{DAOException, EntitéDAO};
use progression\domaine\entité\TentativeSys;

class TentativeSysDAO extends TentativeDAO
{
	public function get_toutes($username, $question_uri)
	{
		$tentatives = [];
		try {
			$query = EntitéDAO::get_connexion()->prepare(
				'SELECT reponse_sys.conteneur,
				reponse_sys.réponse,
				reponse_sys.date_soumission,
				reponse_sys.reussi,
				reponse_sys.tests_reussis
				FROM reponse_sys
				WHERE username = ? 
				AND question_uri = ?',
			);
			$query->bind_param("ss", $username, $question_uri);
			$query->execute();

			$conteneur = null;
			$réponse = null;
			$date_soumission = null;
			$réussi = false;
			$tests_réussis = 0;
			$query->bind_result($conteneur, $réponse, $date_soumission, $réussi, $tests_réussis);

			while ($query->fetch()) {
				$tentatives[] = new TentativeSys($conteneur, $réponse, $date_soumission, $réussi, $tests_réussis);
			}

			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $tentatives;
	}

	public function get_tentative($username, $question_uri, $timestamp)
	{
		$tentative = null;

		try {
			$query = EntitéDAO::get_connexion()->prepare(
				'SELECT reponse_sys.conteneur,
				reponse_sys.réponse,
				reponse_sys.date_soumission,
				reponse_sys.reussi,
				reponse_sys.tests_reussis,
				reponse_sys.temps_exécution
				FROM reponse_sys
				WHERE username = ? 
				AND question_uri = ?
				AND date_soumission = ?',
			);
			$query->bind_param("ssi", $username, $question_uri, $timestamp);
			$query->execute();

			$conteneur = null;
			$réponse = null;
			$date_soumission = null;
			$réussi = false;
			$tests_réussis = 0;
			$temps_exécution = null;
			$query->bind_result($conteneur, $réponse, $date_soumission, $réussi, $tests_réussis, $temps_exécution);

			if ($query->fetch()) {
				$tentative = new TentativeSys(
					$conteneur,
					$réponse,
					$date_soumission,
					$réussi,
					$tests_réussis,
					$temps_exécution,
				);
			}

			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $tentative;
	}

	public function save($username, $question_uri, $objet)
	{
		try {
			$query = EntitéDAO::get_connexion()->prepare(
				"INSERT INTO reponse_sys ( question_uri, username, conteneur, réponse, date_soumission, reussi, tests_reussis, temps_exécution ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )",
			);
			$query->bind_param(
				"ssssiiii",
				$question_uri,
				$username,
				$objet->conteneur,
				$objet->réponse,
				$objet->date_soumission,
				$objet->réussi,
				$objet->tests_réussis,
				$objet->temps_exécution,
			);
			$query->execute();
			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $this->get_tentative($username, $question_uri, $objet->date_soumission);
	}
}
