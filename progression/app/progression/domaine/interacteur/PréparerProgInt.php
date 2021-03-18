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

namespace progression\domaine\interacteur;

use progression\domaine\entité\Exécutable;

class PréparerProgInt extends Interacteur
{
	public function préparer_exécutable($question, $tentative)
	{
		if (array_key_exists($tentative->langage, $question->exécutables)) {
			$code = $this->composer_code_à_exécuter(
				$question->exécutables[$tentative->langage]->code,
				$tentative->code,
			);
			return new Exécutable($code, $tentative->langage);
		} else {
			return null;
		}
	}

	private function composer_code_à_exécuter($code_utilisateur, $code)
	{
		//Insère les TODOs de code dans code_utilisateur
		$orig = explode("\n", $code_utilisateur);
		$code = $code;

		preg_match_all("/\+TODO.*\n((.|\n)*?)\n*(.*-TODO|\Z)/", $code, $todos);

		$n = 0;
		$res = [];
		$todo = false;

		foreach ($orig as $ligne) {
			if ($todo && strpos($ligne, "-TODO")) {
				$todo = false;
			}

			if (!$todo) {
				$res[] = $ligne;
			}

			if (!$todo && strpos($ligne, "+TODO")) {
				$todo = true;
				$res[] = $todos[1][$n++];
			}
		}

		$res = implode("\n", $res);

		return $res;
	}
}
