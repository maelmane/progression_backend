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

use Firebase\JWT\JWT;
use progression\http\contrôleur\GénérateurDeToken;
use progression\domaine\entité\User;

final class GénérateurDeTokenTests extends TestCase
{
  public $user;
  public $expiration;
  public $ressources;

  public function setUp(): void
  {
    parent::setUp();
    $this->user = new User("Pascal");
  }

  public function test_étant_donné_la_création_dun_token_sans_ressources_la_ressource_est_null()
  {
    $token = GénérateurDeToken::get_instance()->générer_token($this->user);
    $tokenDécodé = JWT::decode($token, $_ENV["JWT_SECRET"], ["HS256"]);
    $this->assertEquals(null, $tokenDécodé->ressources);
  }

  public function test_étant_donné_la_création_dun_token_sans_date_dexpiration_la_date_dexpiration_par_défaut_est_0()
  {
    $token = GénérateurDeToken::get_instance()->générer_token($this->user, null);
    $tokenDécodé = JWT::decode($token, $_ENV["JWT_SECRET"], ["HS256"]);
    $this->assertEquals(0, $tokenDécodé->expired);
  }
}
