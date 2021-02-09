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

namespace progression\domaine\entité;

use PHPUnit\Framework\TestCase;

final class QuestionProgEvalTests extends TestCase{
    // lang
    public function test_étant_donné_une_questionProgEval_instancié_avec_lang_langTest_lorsquon_récupère_son_lang_on_obtient_langTest(){
        $questionProgEval = new QuestionProgEval();
        $questionProgEval->lang = "langTest";

        $lang = $questionProgEval->lang;

        $this->assertEquals( "langTest", $lang );
    }
    
    // setup
    public function test_étant_donné_une_questionProgEval_instancié_avec_setup_setupTest_lorsquon_récupère_son_setup_on_obtient_setupTest(){
        $questionProgEval = new QuestionProgEval();
        $questionProgEval->setup = "setupTest";

        $setup = $questionProgEval->setup;

        $this->assertEquals( "setupTest", $setup );
    }
    
    // pre_exec
    public function test_étant_donné_une_questionProgEval_instancié_avec_pre_exec_preExecTest_lorsquon_récupère_son_pre_exec_on_obtient_preExecTest(){
        $questionProgEval = new QuestionProgEval();
        $questionProgEval->pre_exec = "preExecTest";

        $pre_exec = $questionProgEval->pre_exec;

        $this->assertEquals( "preExecTest", $pre_exec );
    }
    
    // pre_code
    public function test_étant_donné_une_questionProgEval_instancié_avec_pre_code_preCodeTest_lorsquon_récupère_son_pre_code_on_obtient_preCodeTest(){
        $questionProgEval = new QuestionProgEval();
        $questionProgEval->pre_code = "preCodeTest";

        $pre_code = $questionProgEval->pre_code;

        $this->assertEquals( "preCodeTest", $pre_code );
    }

    // code_utilisateur
    public function test_étant_donné_une_questionProgEval_instancié_avec_code_utilisateur_codeUtilisateurTest_lorsquon_récupère_son_code_utilisateur_on_obtient_codeUtilisateurTest(){
        $questionProgEval = new QuestionProgEval();
        $questionProgEval->code_utilisateur = "codeUtilisateurTest";

        $code_utilisateur = $questionProgEval->code_utilisateur;

        $this->assertEquals( "codeUtilisateurTest", $code_utilisateur );
    }

    // post_code
    public function test_étant_donné_une_questionProgEval_instancié_avec_post_code_postCodeTest_lorsquon_récupère_son_post_code_on_obtient_postCodeTest(){
        $questionProgEval = new QuestionProgEval();
        $questionProgEval->post_code = "postCodeTest";

        $post_code = $questionProgEval->post_code;

        $this->assertEquals( "postCodeTest", $post_code );
    }

    // solution
    public function test_étant_donné_une_questionProgEval_instancié_avec_solution_solutionTest_lorsquon_récupère_sa_solution_on_obtient_solutionTest(){
        $questionProgEval = new QuestionProgEval();
        $questionProgEval->solution = "solutionTest";

        $solution = $questionProgEval->solution;

        $this->assertEquals( "solutionTest", $solution );
    }

    // stdin
    public function test_étant_donné_une_questionProgEval_instancié_avec_stdin_stdinTest_lorsquon_récupère_son_stdin_on_obtient_stdinTest(){
        $questionProgEval = new QuestionProgEval();
        $questionProgEval->stdin = "stdinTest";

        $stdin = $questionProgEval->stdin;

        $this->assertEquals( "stdinTest", $stdin );
    }

    // params
    public function test_étant_donné_une_questionProgEval_instancié_avec_params_array_paramsTest0_paramsTest1_lorsquon_récupère_le_premier_item_de_params_on_obtient_paramsTest0(){
        $questionProgEval = new QuestionProgEval();
        $questionProgEval->params = array("paramsTest0", "paramsTest1");

        $params = $questionProgEval->params;

        $this->assertEquals( "paramsTest0", $params[0] );
    }

}

?>
