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
use progression\domaine\entité\{Avancement, Question};

class AvancementDAO extends EntitéDAO
{
	public function get_tous($username)
	{
		$avancements = [];

		try {
			$query = EntitéDAO::get_connexion()->prepare(
				"SELECT question_uri, etat, type FROM avancement WHERE username = ?",
			);
			$query->bind_param("s", $username);
			$query->execute();

			$uri = null;
			$etat = 0;
			$type = 0;
			$query->bind_result($uri, $etat, $type);
			while ($query->fetch()) {
				$avancements[$uri] = new Avancement($etat, $type);
			}

			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $avancements;
	}


	public function get_tous_avancements_avec_tentatives_etat_sauvegardes_type($username)
	{
		$avancements = [];
		try {
			$query = EntitéDAO::get_connexion()->prepare(
				"SELECT question_uri, etat, type FROM avancement WHERE username = ?",
			);

			$query->bind_param("s", $username);
			$query->execute();

			$uri = null;
			$etat = 0;
			$type = 0;
			$query->bind_result($uri, $etat, $type);
			while ($query->fetch()) {
				$avancement = new Avancement($etat, $type);
				$avancement->tentatives = $this->get_avancement_sans_load($username, $uri)->tentatives;
				$avancement->sauvegardes = $this->get_avancement_sans_load($username, $uri)->sauvegardes;

				$avancements[$uri] = $avancement;
			}
			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $avancements;
	}
	protected function get_avancement_sans_load($username, $question_uri)
	{
			$avancementBidon = new Avancement();
			$avancementBidon->tentatives = $this->source->get_tentative_dao()->get_toutes($username, $question_uri);
			$avancementBidon->sauvegardes = $this->source->get_sauvegarde_dao()->get_toutes($username, $question_uri);
		

		return $avancementBidon;
	}

	public function get_avancement($username, $question_uri)
	{
		$avancement = $this->load($username, $question_uri);

		if ($avancement) {
			$avancement->tentatives = $this->source->get_tentative_dao()->get_toutes($username, $question_uri);
			$avancement->sauvegardes = $this->source->get_sauvegarde_dao()->get_toutes($username, $question_uri);
		}

		return $avancement;
	}

	protected function load($username, $question_uri)
	{
		$état = null;
		$type = null;
		$avancement = null;

		try {
			$query = EntitéDAO::get_connexion()->prepare(
				"SELECT etat, type FROM avancement WHERE question_uri = ? AND username = ?",
			);
			$query->bind_param("ss", $question_uri, $username);
			$query->execute();
			$query->bind_result($état, $type);

			if ($query->fetch()) {
				$avancement = new Avancement($état, $type);
			}

			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $avancement;
	}

	public function save($username, $question_uri, $objet)
	{
		try {
			$query = EntitéDAO::get_connexion()->prepare(
				"INSERT INTO avancement ( etat, question_uri, username, type ) VALUES ( ?, ?, ?, " .
					Question::TYPE_PROG .
					')
                                              ON DUPLICATE KEY UPDATE etat = VALUES( etat ) ',
			);

			$query->bind_param("iss", $objet->etat, $question_uri, $username);
			$query->execute();
			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $this->get_avancement($username, $question_uri);
	}
}
