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

final class QuestionProgTests extends TestCase{
    public function test_étant_donné_une_questionProg_instanciée_avec_PYTHON3_1_CPP_8_JAVA_10_exécutables_array_execTest0_execTest1_et_tests_testTest0_testTest1_lorsquon_récupère_son_PYTHON3_on_obtient_1(){
        $questionProgTest = new QuestionProg();
        $questionProgTest->exécutables = array("execTest0", "execTest1");
        $questionProgTest->tests = array("testTest0", "testTest1");

        $python3 = $questionProgTest::PYTHON3;

        $this->assertEquals( 1, $python3 );
    }

    public function test_étant_donné_une_questionProg_instanciée_avec_PYTHON3_1_CPP_8_JAVA_10_exécutables_array_execTest0_execTest1_et_tests_testTest0_testTest1_lorsquon_récupère_son_CPP_on_obtient_8(){
        $questionProgTest = new QuestionProg();
        $questionProgTest->exécutables = array("execTest0", "execTest1");
        $questionProgTest->tests = array("testTest0", "testTest1");

        $cpp = $questionProgTest::CPP;

        $this->assertEquals( 8, $cpp );
    }

    public function test_étant_donné_une_questionProg_instanciée_avec_PYTHON3_1_CPP_8_JAVA_10_exécutables_array_execTest0_execTest1_et_tests_testTest0_testTest1_lorsquon_récupère_son_JAVA_on_obtient_10(){
        $questionProgTest = new QuestionProg();
        $questionProgTest->exécutables = array("execTest0", "execTest1");
        $questionProgTest->tests = array("testTest0", "testTest1");

        $java = $questionProgTest::JAVA;

        $this->assertEquals( 10, $java );
    }

    public function test_étant_donné_une_questionProg_instanciée_avec_PYTHON3_1_CPP_8_JAVA_10_exécutables_array_execTest0_execTest1_et_tests_testTest0_testTest1_lorsquon_récupère_le_premier_item_d_exécutables_on_obtient_execTest0(){
        $questionProgTest = new QuestionProg();
        $questionProgTest->exécutables = array("execTest0", "execTest1");
        $questionProgTest->tests = array("testTest0", "testTest1");

        $exécutables = $questionProgTest->exécutables;

        $this->assertEquals( "execTest0", $exécutables[0] );
    }

    public function test_étant_donné_une_questionProg_instanciée_avec_PYTHON3_1_CPP_8_JAVA_10_exécutables_array_execTest0_execTest1_et_tests_testTest0_testTest1_lorsquon_récupère_le_premier_item_de_tests_on_obtient_testTest0(){
        $questionProgTest = new QuestionProg();
        $questionProgTest->exécutables = array("execTest0", "execTest1");
        $questionProgTest->tests = array("testTest0", "testTest1");

        $tests = $questionProgTest->tests;

        $this->assertEquals( "testTest0", $tests[0] );
    }


}

?>
