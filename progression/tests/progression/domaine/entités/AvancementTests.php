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

final class AvancementTests extends TestCase{
    public function test_étant_donné_un_avancement_instancié_avec_questionid_5_lorsquon_récupère_son_questionid_on_obtient_5(){
        $avancementTest = new Avancement(5, 3);

        $questionid = $avancementTest->question_id;

        $this->assertEquals( 5, $questionid );
    }

    public function test_étant_donné_un_avancement_instancié_avec_userid_3_lorsquon_récupère_son_userid_on_obtient_3(){
        $avancementTest = new Avancement(5, 3);

        $userid = $avancementTest->user_id;

        $this->assertEquals( 3, $userid );
    }

}

?>
