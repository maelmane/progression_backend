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

namespace progression\domaine\interacteur;

use progression\domaine\entité\User;
use PHPUnit\Framework\TestCase;
use \Mockery as m;
    
final class LoginIntTests extends TestCase
{
    public function test_étant_donné_un_user_qui_a_été_trouvé_on_obtient_un_objet_user()
    {
        $_ENV['AUTH_TYPE'] = 'no';

        $user = new User();
        $user->username = 'Bob';
        
        $mockUserDao = m::mock( 'progression\dao\UserDAO' );
        $mockUserDao->shouldReceive( 'trouver_par_nomusager' )
            ->with( 'Bob' )
            ->andReturn( $user );

        $mockFactory = m::mock( 'progression\dao\DAOFactory' );
        $mockFactory->shouldReceive( 'get_user_dao' )
            ->andReturn( $mockUserDao );

        $interacteur = new LoginInt( $mockFactory );
        $résultatTest = $interacteur->effectuer_login( $user->username, '' );

        $this->assertEquals( $user, $résultatTest );
    }

    public function test_étant_donné_un_user_qui_na_pas_été_trouvé_on_obtient_un_objet_user()
    {
        $user = new User();
        $user->username = 'Bob';
        
        $mockUserDao = m::mock( 'progression\dao\UserDAO' );
        $mockUserDao->allows()
            ->trouver_par_nomusager( 'Bob' )
            ->andReturn( null );
        $mockUserDao->shouldReceive( 'save' )
            ->andReturn( $user );

        $mockFactory = m::mock( 'progression\dao\DAOFactory' );
        $mockFactory->shouldReceive( 'get_user_dao' )
            ->andReturn( $mockUserDao );

        $interacteur = new LoginInt( $mockFactory );
        $résultatTest = $interacteur->effectuer_login( $user->username, '' );

        $this->assertEquals( $user, $résultatTest );
    }
}
