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

final class AvancementProgTests extends TestCase{
    public function test_étant_donné_un_avancement_prog_instancié_avec_questionid_2_et_userid_4_et_réponses_9_et_lang_8_lorsquon_récupère_son_questionid_on_obtient_2(){
        $avancementProgTest = new AvancementProg(2, 4, 9, 8);

        $questionid = $avancementProgTest->question_id;

        $this->assertEquals( 2, $questionid );
    }

    public function test_étant_donné_un_avancement_prog_instancié_avec_questionid_2_et_userid_4_et_réponses_9_et_lang_8_lorsquon_récupère_son_userid_on_obtient_4(){
        $avancementProgTest = new AvancementProg(2, 4, 9, 8);

        $userid = $avancementProgTest->user_id;

        $this->assertEquals( 4, $userid );
    }

    public function test_étant_donné_un_avancement_prog_instancié_avec_questionid_2_et_userid_4_et_réponses_9_et_lang_8_lorsquon_récupère_son_réponses_on_obtient_9(){
        $avancementProgTest = new AvancementProg(2, 4, 9, 8);

        $réponses = $avancementProgTest->réponses;

        $this->assertEquals( 9, $réponses);
    }

    public function test_étant_donné_un_avancement_prog_instancié_avec_questionid_2_et_userid_4_et_réponses_9_et_lang_8_lorsquon_récupère_son_lang_on_obtient_8(){
        $avancementProgTest = new AvancementProg(2, 4, 9, 8);

        $lang = $avancementProgTest->lang;

        $this->assertEquals( 8, $lang);
    }

    public function test_étant_donné_un_avancement_prog_instancié_avec_questionid_2_et_userid_4_lorsquon_récupère_son_réponses_on_obtient_null(){
        $avancementProgTest = new AvancementProg(2, 4);

        $réponses = $avancementProgTest->réponses;

        $this->assertEquals( null, $réponses);
    }

    public function test_étant_donné_un_avancement_prog_instancié_avec_questionid_2_et_userid_4_etlorsquon_récupère_son_lang_on_obtient_null(){
        $avancementProgTest = new AvancementProg(2, 4);

        $lang = $avancementProgTest->lang;

        $this->assertEquals( null, $lang);
    }
    
}

?>
