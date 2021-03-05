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

final class TestTests extends TestCase{
    // nom
    public function test_étant_donné_un_test_instancié_avec_nom_testNom_lorsquon_récupère_son_nom_on_obtient_testNom(){
        $testTest = new Test("testNom", "testEntrée", "testSortie", array("testParam0", "testParam1"), "testFbp", "testFbn");

        $nom = $testTest->nom;

        $this->assertEquals( "testNom", $nom );
    }

    // entrée
    public function test_étant_donné_un_test_instancié_avec_entrée_testEntrée_lorsquon_récupère_son_entrée_on_obtient_testEntrée(){
        $testTest = new Test("testNom", "testEntrée", "testSortie", array("testParam0", "testParam1"), "testFbp", "testFbn");

        $entrée = $testTest->entrée;

        $this->assertEquals( "testEntrée", $entrée );
    }

    // sortie
    public function test_étant_donné_un_test_instancié_avec_sortie_testSortie_lorsquon_récupère_sa_sortie_on_obtient_testSortie(){
        $testTest = new Test("testNom", "testEntrée", "testSortie", array("testParam0", "testParam1"), "testFbp", "testFbn");

        $sortie = $testTest->sortie_attendue;

        $this->assertEquals( "testSortie", $sortie );
    }

    // params
    public function test_étant_donné_un_test_instancié_avec_params_array_testParam0_testParam1_lorsquon_récupère_son_premier_item_on_obtient_testParam0(){
        $testTest = new Test("testNom", "testEntrée", "testSortie", array("testParam0", "testParam1"), "testFbp", "testFbn");

        $params = $testTest->params;

        $this->assertEquals( "testParam0", $params[0] );
    }

    // feedback_pos
    public function test_étant_donné_un_test_instancié_avec_fbp_testFbp_et_fbn_testFbn_lorsquon_récupère_son_fbp_on_obtient_testFbp(){
        $testTest = new Test("testNom", "testEntrée", "testSortie", array("testParam0", "testParam1"), "testFbp", "testFbn");

        $fbp = $testTest->feedback_pos;

        $this->assertEquals( "testFbp", $fbp );
    }

    // feedback_neg
    public function test_étant_donné_un_test_instancié_fbn_testFbn_lorsquon_récupère_son_fbn_on_obtient_testFbn(){
        $testTest = new Test("testNom", "testEntrée", "testSortie", array("testParam0", "testParam1"), "testFbp", "testFbn");

        $fbn = $testTest->feedback_neg;

        $this->assertEquals( "testFbn", $fbn );
    }

}

?>
