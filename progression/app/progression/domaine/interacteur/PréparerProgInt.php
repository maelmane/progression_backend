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

class PréparerProgInt
{
	public function préparer_exécutable($question, $tentative)
	{
		if (array_key_exists($tentative->langage, $question->exécutables)) {
			$code = $this->composer_code_à_exécuter(
				$question->exécutables[$tentative->langage]->code,
				$tentative->code,
			);
			if ($code != null) {
				return new Exécutable($code, $tentative->langage);
			}
		}
		return null;
	}

	private function composer_code_à_exécuter($ébauche, $code_utilisateur)
	{
		//Insère les TODOs de code_utilisateur dans ébauche
		$orig = explode("\n", $ébauche);
		$code_utilisateur = $code_utilisateur;
		preg_match_all("/\+TODO.*\n((.|\n)*?)\n*(.*-TODO|\Z)/", $code_utilisateur, $todos_utilisateur);
		preg_match_all("/\+TODO.*\n((.|\n)*?)\n*(.*-TODO|\Z)/", $ébauche, $todos_ébauche);
		$n = 0;
		$res = [];
		$todo = false;

		if (count($todos_utilisateur[1]) != count($todos_ébauche[1])) {
			return null;
		}

		foreach ($orig as $ligne) {

			if ($todo && strpos($ligne, "-TODO")) {
				$todo = false;
			}

			if (!$todo) {
				$res[] = $ligne;
			}

			if (!$todo && strpos($ligne, "+TODO")) {
				$todo = true;
				$res[] = $todos_utilisateur[1][$n++];
			}
		}

		$res = implode("\n", $res);

		return $res;
	}
}
