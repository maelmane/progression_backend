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
    public function test_étant_donné_une_questionProg_instanciée_avec_exécutables_array_execTest0_execTest1_lorsquon_récupère_le_premier_item_d_exécutables_on_obtient_execTest0(){
        $questionProgTest = new QuestionProg();
        $questionProgTest->exécutables = array("execTest0", "execTest1");

        $exécutables = $questionProgTest->exécutables;

        $this->assertEquals( "execTest0", $exécutables[0] );
    }

    public function test_étant_donné_une_questionProg_instanciée_avec_tests_testTest0_testTest1_lorsquon_récupère_le_premier_item_de_tests_on_obtient_testTest0(){
        $questionProgTest = new QuestionProg();
        $questionProgTest->tests = array("testTest0", "testTest1");

        $tests = $questionProgTest->tests;

        $this->assertEquals( "testTest0", $tests[0] );
    }


}

?>
