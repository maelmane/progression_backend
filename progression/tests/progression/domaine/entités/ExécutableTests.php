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

final class ExécutableTests extends TestCase{
    public function test_étant_donné_un_exécutable_instancié_avec_code_helloworld_et_lang_1_lorsquon_récupère_son_code_utilisateur_on_obtient_helloworld(){
        $exécutableTest = new Exécutable("helloworld", 1);

        $codeUtilisateur = $exécutableTest->code_utilisateur;

        $this->assertEquals( "helloworld", $codeUtilisateur );
    }

    public function test_étant_donné_un_exécutable_instancié_avec_code_helloworld_et_lang_1_lorsquon_récupère_son_code_exec_on_obtient_helloworld(){
        $exécutableTest = new Exécutable("helloworld", 1);

        $codeExec = $exécutableTest->code_exec;

        $this->assertEquals( "helloworld", $codeExec );
    }

    public function test_étant_donné_un_exécutable_instancié_avec_code_helloworld_et_lang_1_lorsquon_récupère_son_lang_on_obtient_1(){
        $exécutableTest = new Exécutable("helloworld", 1);

        $lang = $exécutableTest->lang;

        $this->assertEquals( 1, $lang );
    }

}

?>
