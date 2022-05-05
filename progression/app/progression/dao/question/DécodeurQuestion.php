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

class DécodeurQuestion
{
	public static function load($question, $infos_question)
	{
		$question->uri = $infos_question["uri"];
		$question->niveau = $infos_question["niveau"] ?? null;
		$question->titre = $infos_question["titre"] ?? null;
		$question->description = $infos_question["description"] ?? null;
		$question->enonce = $infos_question["énoncé"] ?? null;
		$question->auteur = $infos_question["auteur"] ?? null;
		$question->licence = $infos_question["licence"] ?? null;

		$question->feedback_pos = $infos_question["rétroactions"]["positive"] ?? null;
		$question->feedback_neg = $infos_question["rétroactions"]["négative"] ?? null;
		$question->feedback_err = $infos_question["rétroactions"]["erreur"] ?? null;

		return $question;
	}
}
