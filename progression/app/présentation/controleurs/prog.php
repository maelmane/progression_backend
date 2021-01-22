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


require_once __DIR__ . '/controleur.php';
require_once 'domaine/entités/question_prog.php';

class ProgCtl extends Controleur
{
    const LANG_NOMS = [
        "python2",
        "python",
        "ruby",
        "",
        "php",
        "",
        "",
        "go",
        "cpp",
        "c",
        "java",
        "bash",
        "perl",
        "ssh",
        "mysql",
    ];

    function __construct($source, $user_id)
    {
        parent::__construct($source, $user_id);

        $this->à_valider =
            isset($_REQUEST["valider"]) &&
            ((isset($_REQUEST["reponse"]) && $_REQUEST["reponse"] != "") ||
                (isset($_REQUEST["incode"]) && $_REQUEST["incode"] != ""));
        $this->à_exécuter =
            isset($_REQUEST["exécuter"]) &&
            isset($_REQUEST["incode"]) &&
            $_REQUEST["incode"] != "";
        $this->reponse =
            isset($_REQUEST["reponse"]) && $_REQUEST["reponse"] != ""
                ? $_REQUEST["reponse"]
                : null;
        $this->reset =
            isset($_REQUEST["reset"]) && $_REQUEST["reset"] != ""
                ? $_REQUEST["reset"]
                : null;
        $this->langid =
            isset($_REQUEST["langid"]) && $_REQUEST["langid"] != ""
                ? $_REQUEST["langid"]
                : null;
        $this->incode =
            isset($_REQUEST["incode"]) && $_REQUEST["incode"] != ""
                ? $_REQUEST["incode"]
                : null;
        $this->params =
            isset($_REQUEST["params"]) && $_REQUEST["params"] != ""
                ? $_REQUEST["params"]
                : null;
        $this->stdin =
            isset($_REQUEST["stdin"]) && $_REQUEST["stdin"] != ""
                ? $_REQUEST["stdin"]
                : null;
    }

    protected function calculer_sorties($sorties)
    {
        if ($sorties === false) {
            $output = "";
            $erreurs = "Erreur interne. ";
        }

        return [
            "output" => $sorties["stdout"],
            "erreurs" => $sorties["stderr"],
        ];
    }

    protected function resume($in, $lignes_max)
    {
        $lignes = explode("\n", $in);
        $nb_lignes = count($lignes);
        if ($nb_lignes <= $lignes_max) {
            return $in;
        } else {
            $av = round(($lignes_max - 1) / 2);
            $ap = floor(($lignes_max - 1) / 2);
            return implode(
                "\n",
                array_merge(
                    array_slice($lignes, 0, $av),
                    ["..."],
                    array_slice($lignes, -$ap)
                )
            );
        }
    }
}
