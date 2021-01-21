<?php

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
                if (trim($infos['réponse']) == $infos["question"]->solution_courte) {
                    $réussi = true;
                }
            }
        }
        return $réussi;
    }
}

?>
