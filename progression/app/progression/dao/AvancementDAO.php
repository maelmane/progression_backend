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
use progression\dao\models\AvancementMdl;

class AvancementDAO extends EntitéDAO
{
	public function get_tous($username, $includes = [])
	{
		try {
			return $this->construire(AvancementMdl::where("username", $username)->get(), $includes);
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function get_avancement($username, $question_uri, $includes = [])
	{
		try {
			return $this->construire(
				AvancementMdl::where("username", $username)
					->where("question_uri", $question_uri)
					->first(),
				$includes,
			)[0];
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function save($username, $question_uri, $avancement)
	{
		try {
			$objet = [];
			$objet["etat"] = $avancement->état;
			$objet["question_uri"] = $question_uri;
			$objet["username"] = $username;
			$objet["titre"] = $avancement->titre;
			$objet["niveau"] = $avancement->niveau;
			$objet["date_modification"] = $avancement->date_modification;
			$objet["date_reussite"] = $avancement->date_réussite;

			return $this->construire([AvancementMdl::updateOrCreate($objet)])[0];
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public static function construire($data, $includes = [])
	{
		$avancements = [];
		foreach ($data as $avancement) {
			$avancements[$avancement["question_uri"]] = new Avancement(
				$avancement["etat"],
				$avancement["type"],
				in_array("tentatives", $includes) ? TentativeDAO::construire($avancement["tentatives"]) : [],
				in_array("sauvegardes", $includes) ? SauvegardeDAO::construire($avancement["sauvegardes"]) : [],
				$avancement["titre"],
				$avancement["niveau"],
				$avancement["date_modification"],
				$avancement["date_reussite"],
			);
		}

		return $avancements;
	}
}
