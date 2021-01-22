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


require_once __DIR__ . '/prog.php';

class QuestionBDCtl extends ProgCtl
{
    function get_page_infos()
    {
        $infos = array_merge(
            parent::get_page_infos(),
            $this->récupérer_paramètres()
        );

        if ($this->à_exécuter || $this->à_valider) {
            $sorties = $this->connexion_conteneur($infos);
            $infos = array_merge($infos, $this->décoder_réponse($sorties));
            $this->sauvegarder_conteneur($infos);
        }
        if ($this->à_valider) {
            $infos = array_merge(
                $infos,
                $this->traiter_résultats($sorties, $infos)
            );
        }

        return $infos;
    }

    private function sauvegarder_conteneur($infos)
    {
        if ($infos["cont_id"] != "") {
            $this->avancement->set_conteneur($infos["cont_id"]);
        }
    }

    private function connexion_conteneur($infos)
    {
        $url_rc = $this->get_url_compilebox();
        $options_rc = $this->get_options_compilebox($infos);

        $context = stream_context_create($options_rc);
        $comp_resp = file_get_contents($url_rc, false, $context);

        return $comp_resp;
    }

    private function décoder_réponse($réponse)
    {
        $infos_réponse = [];

        $infos_réponse["cont_id"] = trim(
            json_decode($réponse, true)['cont_id']
        );
        $infos_réponse["cont_ip"] = trim(json_decode($réponse, true)['add_ip']);
        $infos_réponse["cont_port"] = trim(
            json_decode($réponse, true)['add_port']
        );
        $infos_réponse["res_validation"] = trim(
            json_decode($réponse, true)['resultat']
        );
        $infos_réponse["output"] = trim(json_decode($réponse, true)['output']);
        $infos_réponse["erreurs"] = trim(json_decode($réponse, true)['errors']);

        return $infos_réponse;
    }

    private function get_url_compilebox()
    {
        return "http://" .
            $GLOBALS['config']['compilebox_hote'] .
            ":" .
            $GLOBALS['config']['compilebox_port'] .
            "/compile"; //TODO à changer?
    }

    private function get_options_compilebox($infos)
    {
        if (
            $this->avancement->get_etat() == Question::ETAT_DEBUT ||
            $this->reset
        ) {
            $data_rc = $this->get_data_nouveau_conteneur($infos);
        } else {
            $data_rc = $this->get_data_conteneur($infos);
        }

        $options_rc = [
            'http' => [
                'header' =>
                    "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data_rc),
            ],
        ];

        return $options_rc;
    }

    private function get_data_nouveau_conteneur($infos)
    {
        return [
            'language' => 14,
            'code' => 'reset',
            'vm_name' => $this->question->image,
            'parameters' => $this->avancement->conteneur,
            'params_conteneur' => $infos['params_conteneur'],
            'stdin' => '',
            'user' => $this->question->user,
        ];
    }

    private function get_data_conteneur($infos)
    {
        //Inutile?
        if (
            is_null($this->question->verification) ||
            $this->question->verification == ""
        ) {
            return [
                'language' => 14,
                'code' => $this->construire_validation(),
                'vm_name' => $this->question->image,
                'parameters' => $this->avancement->conteneur,
                'params_conteneur' => $infos['params_conteneur'],
                'stdin' => '',
                'user' => $this->question->user,
            ];
        } else {
            return [
                'language' => 14,
                'code' => $this->construire_validation(),
                'vm_name' => $this->question->image,
                'parameters' => $this->avancement->conteneur,
                'params_conteneur' => $infos['params_conteneur'],
                'stdin' => '',
                'user' => $this->question->user,
            ];
        }
    }

    private function construire_validation()
    {
        echo $this->question->verification;
        if (
            !is_null($this->question->verification) &&
            $this->question->verification != ""
        ) {
            return str_replace(
                "{reponse}",
                $this->get_réponse_utilisateur(),
                $this->question->verification
            );
        } else {
            return $question->pre_exec .
                "\n" .
                $this->question->pre_code .
                "\n" .
                $this->get_code() .
                "\n" .
                $this->question->post_code;
        }
    }

    private function get_réponse_utilisateur()
    {
        return $this->reponse != null ? $this->reponse : "";
    }

    private function récupérer_paramètres()
    {
        $langid = $this->question->lang;

        eval($this->question->setup);

        $this->question->énoncé = str_replace(
            "\r",
            "",
            eval("return \"$this->question->enonce\";")
        );
        $this->question->solution = str_replace(
            "\r",
            "",
            eval("return $this->question->solution;")
        );

        $infos = [
            "template" => "question_bd",
            "question" => $this->question,
            "avancement" => $this->avancement,
            "params" => $this->question->user,
            "params_conteneur" =>
                "-e MYSQL_ALLOW_EMPTY_PASSWORD=yes --tmpfs /var/lib/mysql:rw",
            "code" => $this->get_code($this->question, $this->avancement),
            "reponse" => $this->get_réponse_utilisateur(),
            "url_retour" => "index.php?p=serie&ID=" . $this->question->serieID,
            "titre_retour" => "la liste de questions",
            "état_réussi" =>
                $this->avancement->get_etat() == Question::ETAT_REUSSI,
            "lang_nom" => ControleurProg::LANG_NOMS[$langid],
            "nom_serveur" => $_SERVER["SERVER_NAME"], //TODO à changer ?
            "url_retour" => "index.php?p=serie&ID=" . $this->question->serieID,
            "titre_retour" => "la liste de questions",
        ];

        return $infos;
    }

    private function compter_lignes($texte)
    {
        if ($texte == "") {
            return 0;
        } else {
            return count(preg_split('/\n/', $texte));
        }
    }

    private function traiter_résultats($sorties, $infos)
    {
        $résultats = [];
        $résultats["essayé"] = "true";

        if (
            !is_null($this->question->verification) &&
            $this->question->verification != ""
        ) {
            if ($this->vérifier_validation($infos)) {
                $résultats["réussi"] = "true";
            } else {
                $résultats["nonréussi"] = "true";
            }
        } elseif (
            !is_null($this->question->solution_courte) &&
            $this->question->solution_courte != ""
        ) {
            if ($this->vérifier_solution_courte($infos)) {
                $résultats["réussi"] = "true";
            } else {
                $résultats["nonréussi"] = "true";
            }
        } else {
            if ($this->vérifier_solution($sorties, $this->question->solution)) {
                $résultats["réussi"] = "true";
            } else {
                $résultats["nonréussi"] = "true";
            }
        }

        if (isset($résultats["réussi"])) {
            $this->sauvegarder_état_réussi(
                $this->get_code(),
                $infos['reponse']
            );
        } else {
            $this->sauvegarder_état_échec($this->get_code(), $infos['reponse']);
        }

        $résultats["état_réussi"] =
            $this->avancement->get_etat() == Question::ETAT_REUSSI;

        return $résultats;
    }

    private function vérifier_solution($sorties, $solution)
    {
        $sortie_standard = $this->extraire_sortie_standard($sorties);
        $sortie_erreur = $this->extraire_sortie_erreur($sorties);

        //en PHP, "" == NULL ( arg!!! )
        return $solution != "null" && $sortie_standard == $solution;
    }

    private function vérifier_validation($infos)
    {
        $réussi = false;

        //validation exécutée
        if (
            $infos['res_validation'] != "" &&
            $infos['res_validation'] == "valide"
        ) {
            return true;
        }

        return false;
    }

    private function vérifier_solution_courte($infos)
    {
        //réponse textuelle
        if (
            !is_null($this->question->solution_courte) &&
            $this->question->solution_courte != "" &&
            $infos['reponse'] != "" &&
            $infos['reponse'] == $this->question->solution_courte
        ) {
            return true;
        }
        return false;
    }

    private function sauvegarder_état_réussi($code, $reponse)
    {
        $this->avancement->set_code($code);
        $this->avancement->set_reponse($reponse);
        $this->avancement->set_etat(Question::ETAT_REUSSI);
    }

    private function sauvegarder_état_échec($code, $reponse)
    {
        //Met la réponse à jour dans l'avancement seulement
        //si la question n'avait pas déjà été réussie
        if ($this->avancement->get_etat() != Question::ETAT_REUSSI) {
            $this->avancement->set_code($code);
            $this->avancement->set_reponse($reponse);
            $this->avancement->set_etat(Question::ETAT_NONREUSSI);
        }
    }

    private function sauvegarder_état_non_réussi($code)
    {
        $this->avancement->set_etat(Question::ETAT_NONREUSSI);
    }
}
?>
