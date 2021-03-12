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

use progression\domaine\entité\{Question, TentativeProg};

class TraiterTentativeProgInt extends Interacteur
{
    function traiter_résultats($exécutable, $tests, $question, $username)
    {
        $résultats = [];

        $avancement = (new ObtenirAvancementInt(
            $this->_source
        ))->get_avancement($question->id, $username);

        $avancement->réponses[$exécutable->lang] = new TentativeProg(
            $exécutable->lang,
            $exécutable->code_utilisateur,
            (new \DateTime())->getTimestamp()
        );

        $résultats["essayé"] = "true";

        $réussi = true;
        foreach ($tests as $test) {
            $test->réussi = $this->vérifier_solution(
                $test->sorties,
                $test->solution
            );
            if (!$test->réussi) {
                $réussi = false;
            }
        }

        if ($réussi) {
            $avancement->etat = Question::ETAT_REUSSI;
            $this->sauvegarder_avancement($avancement);
            $résultats["réussi"] = "true";
        } else {
            if ($avancement->etat != Question::ETAT_REUSSI) {
                $avancement->etat = Question::ETAT_NONREUSSI;
                $this->sauvegarder_avancement($avancement);
            }
            $résultats["nonréussi"] = "true";
        }

        $résultats["état_réussi"] = $avancement->etat == Question::ETAT_REUSSI;

        return $résultats;
    }

    private function vérifier_solution($sorties, $solution)
    {
        $sortie_standard = $sorties["output"];
        $erreur = $sorties["erreurs"];
        //en PHP, "" == NULL ( arg!!! )
        return $solution != "null" &&
            $sortie_standard == $solution &&
            $erreur == "";
    }

    private function sauvegarder_avancement($avancement)
    {
        $interacteur = new SauvegarderAvancementProgInt($this->_source);
        $interacteur->sauvegarder($avancement);
    }
}
