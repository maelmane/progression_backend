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
	public function test_étant_donné_la_création_dun_token_avec_ressources_et_date_dexpiration_un_token_avec_les_informations_correspondantes_est_créé()
	{
		$expectedUsername = "UtilisteurLambda";

		$expirationAttendue = "1648684800";
		$ressourcesAttendue = "ressources";
		$token = GénérateurDeToken::get_instance()->générer_token(
			$expectedUsername,
			$ressourcesAttendue,
			$expirationAttendue,
		);
		$tokenDécodé = JWT::decode($token, $_ENV["JWT_SECRET"], ["HS256"]);
		$this->assertEquals($expectedUsername, $tokenDécodé->username);
		$this->assertEquals($ressourcesAttendue, $tokenDécodé->ressources);
		$this->assertEquals($expirationAttendue, $tokenDécodé->expired);
	}

	public function test_étant_donné_la_création_dun_token_sans_ressource_et_sans_date_dexpiration_la_ressource_est_null_et_la_date_dexpiration_est_0()
	{
		$token = GénérateurDeToken::get_instance()->générer_token("UtilisteurLambda");
		$tokenDécodé = JWT::decode($token, $_ENV["JWT_SECRET"], ["HS256"]);
		$this->assertEquals(null, $tokenDécodé->ressources);
		$this->assertEquals(0, $tokenDécodé->expired);
	}
}
