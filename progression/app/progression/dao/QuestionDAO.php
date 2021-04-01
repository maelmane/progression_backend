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

use progression\domaine\entité\{QuestionProg, QuestionSys, QuestionBD};

class QuestionDAO extends EntitéDAO
{
	public function get_question($uri)
	{
		$infos_question = $this->récupérer_question($uri);

		if ($infos_question === null) {
			return null;
		}

		$question = null;
		if (key_exists("type", $infos_question)) {
			$type = $infos_question["type"];

			if ($type == "prog") {
				$question = new QuestionProg();
				$this->load($question, $infos_question);
				$this->source->get_question_prog_dao()->load($question, $infos_question);
			} elseif ($type == "sys") {
				$question = new QuestionSys();
				$this->load($question, $infos_question);
				$this->source->get_question_sys_dao()->load($question, $infos_question);
			} elseif ($type == "bd") {
				$question = new QuestionBD();
				$this->source->get_question_bd_dao()->load($question, $infos_question);
			}
		} else {
			return null;
		}
		return $question;
	}

	protected function load($question, $infos_question)
	{
		$question->uri = $infos_question["uri"];
		$question->titre = $infos_question["titre"];
		$question->description = $infos_question["description"];
		$question->enonce = $infos_question["énoncé"];
		$question->feedback_pos = key_exists("feedback+", $infos_question) ? $infos_question["feedback+"] : null;
		$question->feedback_neg = key_exists("feedback-", $infos_question) ? $infos_question["feedback-"] : null;
	}

	protected function récupérer_question($uri)
	{
		$data = @file_get_contents($uri . "/info.yml");

		if ($data === false) {
			error_log("$uri ne peut pas être chargé");
			return null;
		}

		$info = yaml_parse($data);
		if ($info === false) {
			error_log("$uri ne peut pas être décodé");
			return null;
		}

		if (isset($info["type"]) && $info["type"] == "prog") {
			$info = $this->source->get_question_prog_dao()->récupérer_question($uri, $info);
		}

		$info["uri"] = $uri;
		return $info;
	}
}
