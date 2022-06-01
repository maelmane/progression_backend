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
use progression\domaine\entité\Sauvegarde;
use progression\dao\models\{AvancementMdl, SauvegardeMdl};

use DB;

class SauvegardeDAO extends EntitéDAO
{
	public function get_toutes($username, $question_uri, $includes = [])
	{
		try {
			return $this->construire(
				SauvegardeMdl::query()
					->where("username", $username)
					->where("question_uri", $question_uri)
					->get(),
				$includes,
			);
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function get_sauvegarde($username, $question_uri, $langage, $includes = [])
	{
		try {
			$sauvegarde = SauvegardeMdl::query()
				->where("username", $username)
				->where("question_uri", $question_uri)
				->where("langage", $langage)
				->first();
			if ($sauvegarde) {
				return $this->construire([$sauvegarde], $includes)[$langage];
			} else {
				return null;
			}
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public function save($username, $question_uri, $langage, $sauvegarde)
	{
		try {
			DB::update(
				"
                INSERT INTO sauvegarde (
                    username,
                    question_uri,
                    date_sauvegarde,
                    langage,
                    code,
                    avancement_id )
				VALUES (
                    :username,
                    :question_uri,
                    :date_sauvegarde,
                    :langage,
                    :code,
                    (SELECT id FROM avancement WHERE username=:username_i AND question_uri=:question_uri_i) )
				ON DUPLICATE KEY UPDATE
                    code = VALUES( code ),
                    date_sauvegarde = VALUES( date_sauvegarde )",
				[
					"username" => $username,
					"question_uri" => $question_uri,
					"date_sauvegarde" => $sauvegarde->date_sauvegarde,
					"langage" => $langage,
					"code" => $sauvegarde->code,
					"username_i" => $username,
					"question_uri_i" => $question_uri,
				],
			);

			return $this->get_sauvegarde($username, $question_uri, $langage);
		} catch (QueryException $e) {
			throw new DAOException($e);
		}
	}

	public static function construire($data, $includes = [])
	{
		if ($data == null) {
			return [];
		}

		$sauvegardes = [];
		foreach ($data as $i => $item) {
			$sauvegardes[$item["langage"]] = new Sauvegarde($item["date_sauvegarde"], $item["code"]);
		}

		return $sauvegardes;
	}
}
