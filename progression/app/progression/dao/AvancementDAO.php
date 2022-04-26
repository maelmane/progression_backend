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
use progression\domaine\entité\{Avancement, Question, TentativeProg};

class AvancementDAO extends EntitéDAO
{

	const QUERY_SELECT = "avancement.question_uri, avancement.etat, avancement.type, avancement.titre, avancement.niveau, avancement.date_modification, avancement.date_reussite ";
	const QUERY_FROM = "JOIN avancement ON user.username = avancement.username ";

	public static function construire_avancement( $data ){
		return new Avancement(
			$data["etat"],
			$data["type"],
			[],
			[],
			$data["titre"],
			$data["niveau"],
			$data["date_modification"],
			$data["date_reussite"]);
	}
	
	public function get_tous($username, $includes=[])
	{
		$avancements = [];

		try {
			$query = EntitéDAO::get_connexion()->prepare(
				"SELECT avancement.question_uri, etat, type, titre, niveau, date_modification, date_reussite, langage, code, date_soumission, reussi, tests_reussis FROM avancement JOIN reponse_prog ON avancement.username = reponse_prog.username AND avancement.question_uri = reponse_prog.question_uri WHERE avancement.username = ?",
			);
			$query->bind_param("s", $username);
			$query->execute();

			$uri = null;
			$etat = QUESTION::ETAT_DEBUT;
			$type = QUESTION::TYPE_INCONNU;
			$titre = "";
			$niveau = "";
			$date_modification = 0;
			$date_réussite = 0;
			$langage = null;
			$code = null;
			$date_soumission = null;
			$réussi = false;
			$tests_réussis = 0;
			$query->bind_result($uri, $etat, $type, $titre, $niveau, $date_modification, $date_réussite, $langage, $code, $date_soumission, $réussi, $tests_réussis);
			while ($query->fetch()) {
				if( ! in_array($uri, $avancements)){
					$avancements[$uri] = new Avancement($etat, $type);
					$avancements[$uri]->titre = $titre;
					$avancements[$uri]->niveau = $niveau;
					$avancements[$uri]->date_modification = $date_modification;
					$avancements[$uri]->date_réussite = $date_réussite;
				}
				$avancements[$uri]->tentatives[$date_soumission] = new TentativeProg(
					$langage,
					$code,
					$date_soumission,
					$réussi,
					$tests_réussis					
				);
			}

			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $avancements;
	}

	public function get_avancement($username, $question_uri, $includes=[])
	{
		$avancement = $this->load($username, $question_uri);

		if ($avancement) {
			if (in_array("tentatives", $includes))
				$avancement->tentatives = $this->source->get_tentative_dao()->get_toutes($username, $question_uri);
			if (in_array("sauvegardes", $includes))
				$avancement->sauvegardes = $this->source->get_sauvegarde_dao()->get_toutes($username, $question_uri);
		}

		return $avancement;
	}

  	protected function load($username, $question_uri)
	{
		$état = QUESTION::ETAT_DEBUT;
		$type = QUESTION::TYPE_INCONNU;
		$titre = "";
		$niveau = "";
		$date_modification = 0;
		$date_réussite = 0;
		$avancement = null;

		try {
			$query = EntitéDAO::get_connexion()->prepare(
				"SELECT etat, type, titre, niveau, date_modification, date_reussite FROM avancement WHERE question_uri = ? AND username = ?",
			);
			$query->bind_param("ss", $question_uri, $username);
			$query->execute();
			$query->bind_result($état, $type, $titre, $niveau, $date_modification, $date_réussite);

			if ($query->fetch()) {
				$avancement = new Avancement();
				$avancement->etat = $état;
				$avancement->type = $type;
				$avancement->titre = $titre;
				$avancement->niveau = $niveau;
				$avancement->date_modification = $date_modification;
				$avancement->date_réussite = $date_réussite;
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
				"INSERT INTO avancement ( etat, question_uri, username, titre, niveau, date_modification, date_reussite, type ) VALUES ( ?, ?, ?, ?, ?, ?, ?, " .
				Question::TYPE_PROG .
				')
                                              ON DUPLICATE KEY UPDATE etat = VALUES( etat ), date_modification = VALUES(date_modification), date_reussite = VALUES(date_reussite)',
			);

			$query->bind_param(
				"issssii",
				$objet->etat,
				$question_uri,
				$username,
				$objet->titre,
				$objet->niveau,
				$objet->date_modification,
				$objet->date_réussite,
			);
			$query->execute();
			$query->close();
		} catch (mysqli_sql_exception $e) {
			throw new DAOException($e);
		}

		return $this->get_avancement($username, $question_uri);
	}
}
