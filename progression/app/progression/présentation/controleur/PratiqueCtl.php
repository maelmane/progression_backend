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

namespace progression\présentation\controleur;

use progression\domaine\interacteur\ExécuterProgInt;
use progression\domaine\entité\{Exécutable, Test};

class PratiqueCtl extends ProgCtl
{
    function get_page_infos()
    {
        $infos = array_merge(
            parent::get_page_infos(),
            $this->récupérer_paramètres()
        );

        $exécutable = $this->get_exécutable();
        $test = $this->get_test();

        if ($this->à_valider) {
            $sorties = (new ExécuterProgInt(
                $this->_source,
                $this->_user_id
            ))->exécuter($exécutable, $test);

            $test->sorties = $this->calculer_sorties($sorties);

            $infos = array_merge($infos, [
                "résultats" => ["essayé" => "true"],
                "exécutable" => $exécutable,
                "tests" => [$test],
            ]);
        }
        return $infos;
    }

    protected function get_code()
    {
        return isset($_REQUEST["incode"]) ? $_REQUEST["incode"] : "";
    }

    protected function get_params()
    {
        return isset($_REQUEST["params"]) ? $_REQUEST["params"] : "";
    }

    protected function get_stdin()
    {
        return isset($_REQUEST["stdin"]) ? $_REQUEST["stdin"] : "";
    }

    protected function get_langid()
    {
        return isset($_REQUEST["langid"]) ? $_REQUEST["langid"] : "";
    }

    private function récupérer_paramètres()
    {
        $infos = [
            "template" => "pratique",
            "question.titre" => "Zone de pratique libre",
            "question.enonce" => "Cette zone permet d'exécuter vos programmes dans le langage de votre choix. 
                            <br>
                            <em>Attention, vos programmes entrés ici ne seront pas sauvegardés.</em>",
            "url_retour" => "index.php?p=accueil",
            "titre_retour" => "l'accueil",
            "langid" => $this->langid,
            "incode" => $this->get_code(),
            "langages" => ["python2"=>true, "python"=>true, "java"=>true, "cpp"=>true, "perl"=>true, "ruby"=>true, "bash"=>true, "go"=>true],
            
        ];

        return $infos;
    }

    private function get_exécutable()
    {
        $exécutable = new Exécutable($this->get_code(), $this->get_langid());
        $exécutable->stdin = $this->get_stdin();

        return $exécutable;
    }

    private function get_test()
    {
        $test = new Test("Résultats", $_REQUEST["stdin"], false, $this->get_params());

        return $test;
    }
}
