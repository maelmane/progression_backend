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

final class UserTests extends TestCase{
    public function test_étant_donné_un_utilisateur_instancié_avec_username_bob_lorsquon_récupère_son_username_on_obtient_bob(){
        $userTest = new User("bob");

        $username = $userTest->username;

        $this->assertEquals( "bob", $username );
    }

    public function test_étant_donné_un_utilisateur_instancié_avec_role_par_défaut_lorsquon_récupère_son_role_on_obtient_User_ROLE_NORMAL(){
        $userTest = new User("bob");

        $role = $userTest->role;

        $this->assertEquals( $userTest::ROLE_NORMAL, $role );
    }

}

?>
