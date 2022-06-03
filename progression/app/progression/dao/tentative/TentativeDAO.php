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

use DB;
use Illuminate\Database\QueryException;
use progression\dao\{DAOException, EntitéDAO, SauvegardeDAO, CommentaireDAO};
use progression\domaine\entité\Question;
use progression\domaine\entité\{Tentative, TentativeProg, TentativeSys, TentativeBD};

class TentativeDAO extends EntitéDAO
{
	const TYPES = ["eval" => 0, "sys" => 1, "bd" => 2, "prog" => 3];

	public function get_toutes($username, $question_uri)
	{
		$type = $this->get_type($username, $question_uri);

		if ($type == Question::TYPE_PROG) {
			return $this->source->get_tentative_prog_dao()->get_toutes($username, $question_uri);
		} elseif ($type == Question::TYPE_SYS) {
			return $this->source->get_tentative_sys_dao()->get_toutes($username, $question_uri);
		} elseif ($type == Question::TYPE_BD) {
			return $this->source->get_tentative_bd_dao()->get_toutes($username, $question_uri);
		} else {
			return [];
		}
	}

	public function get_tentative($username, $question_uri, $date)
	{
		$type = $this->get_type($username, $question_uri);

		if ($type == Question::TYPE_PROG) {
			return $this->source->get_tentative_prog_dao()->get_tentative($username, $question_uri, $date);
		} elseif ($type == Question::TYPE_SYS) {
			return $this->source->get_tentative_sys_dao()->get_tentative($username, $question_uri, $date);
		} elseif ($type == Question::TYPE_BD) {
			return $this->source->get_tentative_bd_dao()->get_tentative($username, $question_uri, $date);
		} else {
			return null;
		}
	}

	public function save($username, $question_uri, $objet)
	{
		if ($objet instanceof TentativeProg) {
			return $this->source->get_tentative_prog_dao()->save($username, $question_uri, $objet);
		} elseif ($objet instanceof TentativeSys) {
			return $this->source->get_tentative_sys_dao()->save($username, $question_uri, $objet);
		} elseif ($objet instanceof TentativeBD) {
			return $this->source->get_tentative_bd_dao()->save($username, $question_uri, $objet);
		} else {
			return null;
		}
	}

	private function get_type($username, $question_uri)
	{
		try {
            
			$type = DB::select("SELECT type FROM avancement JOIN user ON avancement.user_id = user.id WHERE question_uri = ? AND username = ?", [$question_uri, $username])[0]->type ?? null;
            
            return array_key_exists($type, self::TYPES) ? self::TYPES[$type] : null;

		} catch (QueryException $e) {
			throw new DAOException($e);
		}

	}
}
