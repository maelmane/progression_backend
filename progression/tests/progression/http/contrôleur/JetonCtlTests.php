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

use progression\TestCase;

use progression\http\contrôleur\GénérateurDeToken;
use progression\domaine\entité\{User};
use Illuminate\Http\Request;
use Illuminate\Auth\GenericUser;
use Firebase\JWT\JWT;
use progression\http\contrôleur\JetonCtl;

final class JetonCtlTests extends TestCase
{
	public function test_création_de_jeton_qui_donne_accès_à_un_avancement() {
    putenv("AUTH_LDAP=true");
    putenv("AUTH_LOCAL=true");
    
    $user = new GenericUser(["username" => "MrGeneric", "rôle" => User::ROLE_NORMAL]);
    
    $response = $this->actingAs($user)->call("POST", "/jeton/MrGeneric", ["username" => "MrGeneric", "idRessource" => "IdentifiantRessource", "typeRessource" => "avancement"]);
    
    $this->assertEquals(200, $response->status());
	}
}