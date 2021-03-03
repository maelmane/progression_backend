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

        $type = $infos_question["type"];

		if ($type == null) {
			return null;
		} else {
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

            return $question;
		}
	}

    protected function load($question, $infos_question)
    {
        $question->uri = $infos_question["uri"];
        $question->titre = $infos_question["titre"];
        $question->description = $infos_question["description"];
        $question->enonce = $infos_question["énoncé"];
        $question->feedback_pos = $infos_question["feedback+"];
        $question->feedback_neg = $infos_question["feedback-"];
    }

    protected function récupérer_question($uri){
        $data = file_get_contents($uri . "/info.yml");
        $info = yaml_parse($data);
        
        if(isset($info["type"]) && $info["type"] == Question::TYPE_PROG)
        {
            $info["execs"] = $this->récupérer_execs($uri, $info["execs"]);
            $info["tests"] = $this->récupérer_tests($uri, $info["tests"]);
        }

        $info['uri'] = $uri;
        return $info;
    }

    protected function récupérer_execs($uri, $execs){
        $items=[];

        foreach($execs as $exec){
            $items[$exec["langage"]] = $this->récupérer_exec($uri, $exec["fichier"]);
        }

        return $items;
    }

    protected function récupérer_exec($uri, $exec){
        $data = file_get_contents($uri . "/" . $exec);
        $info = yaml_parse($data);

        return $info;
    }

    protected function récupérer_tests($uri, $tests){
        $items=[];

        foreach($tests as $test){
            $data = file_get_contents($uri . "/" . $test);
            $items = array_merge($items, yaml_parse($data,-1));
        }

        return $items;
    }
    
}
