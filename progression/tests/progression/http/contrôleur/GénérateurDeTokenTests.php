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

use progression\ContrôleurTestCase;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use progression\http\contrôleur\GénérateurDeToken;

final class GénérateurDeTokenTests extends ContrôleurTestCase
{
	public function test_étant_donné_un_nom_dutilisateur_une_ressource_et_une_date_dexpiration_lorsquon_génère_un_token_on_obtient_un_token_avec_les_informations_correspondantes()
	{
		$usernameAttendu = "utilisateur_lambda";
		$expirationAttendue = "1648684800";
		$ressourcesAttendue = json_decode(
			json_encode([
				"permissions" => [
					["url" => "url/ressource", "method" => "GET"],
					["url" => "url/autre_ressource", "method" => "POST"],
				],
			]),
		);

		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda", 1648684800, [
			"permissions" => [
				["url" => "url/ressource", "method" => "GET"],
				["url" => "url/autre_ressource", "method" => "POST"],
			],
		]);

		$tokenDécodé = JWT::decode($token, getenv("JWT_SECRET"), ["HS256"]);

		$this->assertEquals($usernameAttendu, $tokenDécodé->username);
		$this->assertEquals($expirationAttendue, $tokenDécodé->expired);
		$this->assertEquals($ressourcesAttendue, $tokenDécodé->ressources);
	}

	public function test_étant_donné_un_token_généré_avec_un_nom_dutilisateur_seulement_lorsquon_génère_un_token_on_obtient_un_token_avec_ses_valeurs_par_défaut_sauf_le_nom_dutilisateur()
	{
		$expirationAttendue = "0";
		$ressourcesAttendue = (object) [
			"tout" => (object) ["url" => ".*", "method" => ".*"],
		];
		$token = GénérateurDeToken::get_instance()->générer_token("utilisateur_lambda");
		$tokenDécodé = JWT::decode($token, new Key(getenv("JWT_SECRET"), "HS256"));
		$this->assertEquals($ressourcesAttendue, $tokenDécodé->ressources);
		$this->assertEquals($expirationAttendue, $tokenDécodé->expired);
	}
}
