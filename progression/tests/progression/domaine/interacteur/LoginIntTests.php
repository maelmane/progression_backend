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
    public function test_étant_donné_un_user_trouvé_par_son_username_on_obtient_un_objet_user_correspondant()
    {
        $_ENV['AUTH_TYPE'] = 'no';

        $résultat = new User();
        $résultat->username = 'Bob';
        
        $mockUserDao = m::mock( 'progression\dao\UserDAO' );
        $mockUserDao->shouldReceive( 'trouver_par_nomusager' )
            ->with( $résultat->username )
            ->andReturn( $résultat );

        $mockFactory = m::mock( 'progression\dao\DAOFactory' );
        $mockFactory->shouldReceive( 'get_user_dao' )
            ->andReturn( $mockUserDao );

        $interacteur = new LoginInt( $mockFactory );
        $résultatTest = $interacteur->effectuer_login( $résultat->username, '' );

        $this->assertEquals( $résultat, $résultatTest );
    }

    public function test_étant_donné_un_user_non_trouvé_par_son_username_on_obtient_un_objet_user()
    {
        $résultat = new User();
        $résultat->username = 'Banane';
        
        $mockUserDao = m::mock( 'progression\dao\UserDAO' );
        $mockUserDao->allows()
            ->trouver_par_nomusager( $résultat->username )
            ->andReturn( null );
        $mockUserDao->shouldReceive( 'save' )
            ->andReturn( $résultat );

        $mockFactory = m::mock( 'progression\dao\DAOFactory' );
        $mockFactory->shouldReceive( 'get_user_dao' )
            ->andReturn( $mockUserDao );

        $interacteur = new LoginInt( $mockFactory );
        $résultatTest = $interacteur->effectuer_login( $résultat->username, '' );

        $this->assertEquals( $résultat, $résultatTest );
    }
}
