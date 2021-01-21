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
?><?php

require_once __DIR__ . '/obtenir_avancement_sys.php';
require_once __DIR__ . '/sauvegarder_avancement_sys.php';

class TraiterRésultatsSysInt extends Interacteur
{
    function vérifier_réussite($infos)
    {
        $résultats = ['essayé' => "true"];

        $réussi = $this->vérifier_réponse($infos);
        if ($réussi) {
            $résultats["réussi"] = "true";
        } else {
            $résultats["nonréussi"] = "true";
        }

        //récupère l'état d'avancement
        if ($infos["avancement"]->etat == Question::ETAT_REUSSI) {
            $résultats["état_réussi"] = "true";
        } else {
            $résultats["état_réussi"] = "";
        }

        return $résultats;
    }

    private function vérifier_réponse($infos)
    {
        $réussi = false;

        //validation exécutée
        if (
            $infos['res_validation'] != "" &&
            $infos['res_validation'] == "valide"
        ) {
            $réussi = true;
        }
        //réponse textuelle
        elseif (
            !is_null($infos["question"]->solution_courte) &&
            $infos["question"]->solution_courte != ""
        ) {
            if ($infos['réponse'] != '') {
                if ($infos['réponse'] == $infos["question"]->solution_courte) {
                    $réussi = true;
                }
            }
        }
        return $réussi;
    }
}

?>
