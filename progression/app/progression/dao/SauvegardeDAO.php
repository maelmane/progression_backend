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

class SauvegardeDAO extends EntitéDAO
{
	public function get_toutes($username, $question_uri, $includes = [])
	{
		try {
            return $this->construire(
				SauvegardeMdl::select("sauvegarde.*")
				->with( $includes )
                ->join("avancement",
                       "sauvegarde.avancement_id", "=", "avancement.id")
                ->join("user",
                       "avancement.user_id", "=", "user.id")
                ->where("user.username", $username)
                ->where("avancement.question_uri", $question_uri)
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
			$sauvegarde =
                        SauvegardeMdl::select("sauvegarde.*")
                        ->join("avancement",
                               "sauvegarde.avancement_id", "=", "avancement.id")
                        ->join("user",
                               "avancement.user_id", "=", "user.id")
                        ->where("user.username", $username)
                        ->where("avancement.question_uri", $question_uri)
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
            $avancement_id=AvancementMdl::select("avancement.id")
                          ->from("avancement")
                          ->join("user", "avancement.user_id", "=", "user.id")
                          ->where("user.username", $username)
                          ->where("question_uri", $question_uri)
                          ->first()["id"];
            $objet=[
                "date_sauvegarde" => $sauvegarde->date_sauvegarde,
                "langage" => $langage,
                "code" => $sauvegarde->code,
                "avancement_id" => $avancement_id
            ];
            
			return $this->construire([
				SauvegardeMdl::updateOrCreate(["avancement_id" => $avancement_id, "langage" => $langage], $objet)
			])[$langage];
            
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
