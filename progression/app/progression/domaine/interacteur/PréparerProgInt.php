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

class PréparerProgInt
{
    public function préparer_exécutable($question, $avancement, $langage, $code)
    {
        $exécutable = $question->exécutables[$langage];

        $exécutable->code_utilisateur = PréparerProgInt::sélectionner_code_utilisateur(
            $exécutable,
            $avancement,
            $code
        );
        $exécutable->code_exec = $exécutable->code_utilisateur;

        return $exécutable;
    }

    private function sélectionner_code_utilisateur(
        $exécutable,
        $avancement,
        $code
    ) {
        if ($code != null) {
            return PréparerProgInt::composer_code_à_exécuter(
                $exécutable->code_utilisateur,
                $code
            );
        } elseif ($code == null && array_key_exists($exécutable->lang, $avancement->réponses )) {
            return $avancement->réponses[$exécutable->lang]->code;
        } else {
            return $exécutable->code_utilisateur;
        }
    }

    private function composer_code_à_exécuter($code_utilisateur, $code)
    {
        //Insère les TODOs de code dans code_utilisateur
        $orig = explode("\n", $code_utilisateur);
        $code = $code;

        error_log($code);
        preg_match_all("/\+TODO.*\n((.|\n)*?)\n*(.*-TODO|\Z)/", $code, $todos);
        error_log($todos[1][0]);
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

        $res= implode("\n", $res);

        return $res;
    }
}
?>
