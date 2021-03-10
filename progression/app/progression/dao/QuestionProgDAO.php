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

use progression\domaine\entité\{QuestionProg, Exécutable, Test};

class QuestionProgDAO extends QuestionDAO
{

    public $type = QuestionProg::TYPE_PROG;
    
	protected function load($question, $infos_question)
	{
		parent::load($question, $infos_question);

		$question->exécutables = $this->load_exécutables($question, $infos_question);
		$question->tests = $this->load_tests($question, $infos_question);
	}

	protected function load_exécutables($question, $infos_question)
	{
		$exécutables = [];
		foreach ($infos_question["execs"] as $lang => $code) {
			$exécutables[$lang] = new Exécutable($code, $lang);
		}

		return $exécutables;
	}

	protected function load_tests($question, $infos_question)
	{
		$tests = [];
		foreach ($infos_question["tests"] as $test) {
			$tests[] = new Test(
				$test["nom"],
				$test["in"],
				$test["out"],
				key_exists("params", $test) ? $test["params"] : null,
				key_exists("feedback+", $test) ? $test["feedback+"] : null,
                key_exists("feedback-", $test) ? $test["feedback-"] : null,
			);
		}

		return $tests;
	}
}
