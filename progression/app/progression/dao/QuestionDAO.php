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
namespace progression\dao;

use progression\domaine\entité\{Question, QuestionProg, QuestionSys, QuestionBD};

class QuestionDAO
{
    public function get_question($uri)
    {
        $infos_question = $this->récupérer_question($uri);

        if ($infos_question === FALSE ){
            return null;
        }

        $question = null;
		if (key_exists("type", $infos_question)) {
            $type = $infos_question["type"];

            if ($type == Question::TYPE_PROG) {
                $question = new QuestionProg();
                (new QuestionProgDAO())->load($question, $infos_question);
            } elseif ($type == Question::TYPE_SYS) {
                $question = new QuestionSys();
                (new QuestionSysDAO())->load($question, $infos_question);
            } elseif ($type == Question::TYPE_BD) {
                $question = new QuestionBD();
                (new QuestionBDDAO())->load($question, $infos_question);
            }
        }
        else{
            return null;
        }
        return $question;
    }

    protected function load($question, $infos_question)
    {
        $question->uri = $infos_question["uri"];
        $question->type = $this->type;
        $question->titre = $infos_question["titre"];
        $question->description = $infos_question["description"];
        $question->enonce = $infos_question["énoncé"];
        $question->feedback_pos = key_exists("feedback+", $infos_question) ? $infos_question["feedback+"] : null;
        $question->feedback_neg = key_exists("feedback-", $infos_question) ? $infos_question["feedback-"] : null;
    }

    protected function récupérer_question($uri){
        $data = file_get_contents($uri . "/info.yml");

        if($data === FALSE){
            error_log( "$uri ne peut pas être chargé" );
            return null;
        }

        $info = yaml_parse($data);
        if($info == false){
            error_log( "$uri ne peut pas être décodé" );
            return null;
        }

        if(isset($info["type"]) && $info["type"] == Question::TYPE_PROG)
        {
            $exécutables = $this->récupérer_execs($uri, $info["execs"]);
            if ($exécutables == null){
                return null;
            }
            else {
                $info["execs"] = $exécutables;
            }

            $tests = $this->récupérer_tests($uri, $info["tests"]);
            if  ($tests == null){
                return null;
            }
            else {
                $info["tests"] = $tests;
            }
        }

        $info['uri'] = $uri;
        return $info;
    }

    protected function récupérer_execs($uri, $execs){
        $items=[];

        foreach($execs as $exec){
            $exécutable = $this->récupérer_exec($uri, $exec["fichier"]);

            if ($exécutable == null){
                return null;
            }
            
            $items[$exec["langage"]] = $exécutable;
        }

        return $items;
    }

    protected function récupérer_exec($uri, $exec){
        $data = file_get_contents($uri . "/" . $exec);

        if ($data === FALSE){
            error_log( "$uri/$exec ne peut pas être chargé" );
            return null;
        }
        else { 
            return $data;
        }
    }

    protected function récupérer_tests($uri, $tests){
        $items=[];

        foreach($tests as $test){
            $data = file_get_contents($uri . "/" . $test);

            if($data === FALSE) {
                error_log( "$uri/$test ne peut pas être chargé" );
                return null;
            }
            
            $items = array_merge($items, yaml_parse($data,-1));
            if($items == false) {
                error_log( "$uri/$test ne peut pas être décodé" );
                return null;
            }
        }

        return $items;
    }
    
}
