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
use progression\domaine\entité\{User};

final class GénérateurDeTokenTests extends TestCase
{
  public $user;
  public $expiration;
  public $ressources;

  public function setUp(): void
  {
    parent::setUp();

    $this->user = new User("Pascal");
    $this->expiration = time() + $_ENV["JWT_TTL"];
    $this->ressources = '{
          "username": "Pascal",
          "current": "2022-03-28T00:00:00.000Z",
          "expired": "2024-03-28T00:00:00.000Z",
          "ressources": [
            {
              "url": "avancement/username/uri_question",
              "method": "GET"
            },
            {
              "id": "tentative/username/uri_question/date_soumission",
              "method": "GET"
            },
            {
              "url": "commentaire/username/question_uri/date_soumission/numéro",
              "method": "POST"
            }
          ]
        }';
  }

  public function test_étant_donné_la_création_dun_token_avec_ressources_avec_un_user_le_token_contient_le_bon_username()
  {
    $token = GénérateurDeToken::get_instance()->générer_token($this->user, $this->ressources, $this->expiration);
    //print_r($_ENV["JWT_SECRET"]);
    //print_r($token);
    $tokenDécodé = JWT::decode($token, $_ENV["JWT_SECRET"], ["HS256"]);
    $this->assertEquals($this->user->username, $tokenDécodé->username);
  }

  public function test_étant_donné_la_création_dun_token_avec_ressources_le_token_contient_les_ressources()
  {
    $token = GénérateurDeToken::get_instance()->générer_token($this->user, $this->ressources, $this->expiration);
    $tokenDécodé = JWT::decode($token, $_ENV["JWT_SECRET"], ["HS256"]);
    $this->assertEquals($tokenDécodé->ressources, $this->ressources);
  }

  public function test_étant_donné_la_création_dun_token_avec_date_dexpiration_le_token_contient_la_bonne_date_dexpiration()
  {
    $token = GénérateurDeToken::get_instance()->générer_token($this->user, $this->ressources, $this->expiration);
    $tokenDécodé = JWT::decode($token, $_ENV["JWT_SECRET"], ["HS256"]);
    $this->assertEquals($tokenDécodé->expired, $this->expiration);
  }
}
