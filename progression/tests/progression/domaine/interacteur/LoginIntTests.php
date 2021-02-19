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
    public function test_étant_donné_lauthentification_sans_mot_de_passe_lorsquon_authentifie_un_nouvel_utilisateur_il_est_créé(){
        $_ENV['AUTH_TYPE'] = 'no';

        $user = new User( 1 );
        $user->username = "Bob";
        $user->role = 0;
        
        $mockUserDao = m::mock( 'progression\dao\UserDAO' );
        $mockUserDao->shouldReceive( 'save' );
        $mockUserDao->allows()->trouver_par_nomusager('Bob')->andReturn( $user );

        $mockFactory = m::mock( 'progression\dao\DAOFactory' );
        $mockFactory->allows()->get_user_dao()->andReturn( $mockUserDao );

        $interacteur = new LoginInt( $mockFactory );
        $résultatTest = $interacteur->effectuer_login( "Bob", "" );

        $this->assertEquals( $user, $résultatTest );
    }
}
