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

namespace progression\dao\question;

use progression\domaine\entité\{QuestionSys, TestSys};
use DomainException;

class DécodeurQuestionSys extends DécodeurQuestion
{
	public static function load($question, $infos_question)
	{
		parent::load($question, $infos_question);

		$question->utilisateur = $infos_question["utilisateur"];
		$question->image = $infos_question["image"];
		if (isset($infos_question["solution_courte"])) {
			$question->solution_courte = $infos_question["solution_courte"];
		}
		$question->tests = self::load_tests($infos_question);

		if (count($question->tests) == 0) {
			throw new DomainException("Le fichier ne peut pas être décodé. Les tests sont manquants.");
		}

		return $question;
	}

	protected static function load_tests($infos_question)
	{
		$tests = [];
		foreach ($infos_question["tests"] as $i => $test) {
			$tests[] = new TestSys(
				$test["nom"] ?? "#" . $i + 1,
				$test["sortie"],
				$test["validation"],
				$test["utilisateur"] ?? null,
				$test["feedback_pos"] ?? null,
				$test["feedback_neg"] ?? null,
				$test["feedback_err"] ?? null,
			);
		}

		return $tests;
	}
}
