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

final class EntitéTests extends TestCase{
    public function test_étant_donné_une_entité_instancié_avec_id_3_lorsquon_récupère_son_id_on_obtient_3(){
        $entitéTest = new Entité(3);

        $id = $entitéTest->id;

        $this->assertEquals( 3, $id );
    }

    public function test_étant_donné_une_entité_instancié_sans_paramètre_lorsquon_récupère_son_id_on_obtient_null(){
        $entitéTest = new Entité();

        $id = $entitéTest->id;

        $this->assertEquals( null, $id );
    }
    
}

?>
