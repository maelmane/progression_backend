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

namespace progression\http\transformer;

use progression\domaine\entité\User;
use progression\domaine\entité\Clé;
use PHPUnit\Framework\TestCase;

final class UserTransformerTests extends TestCase
{
	public function test_étant_donné_un_user_instancié_avec_id_2_et_nom_bob_lorsquon_récupère_son_transformer_on_obtient_un_objet_json_correspondant()
	{
		$_ENV["APP_URL"] = "https://example.com/";
		$user = new User("bob");
		$user->id = "bob";

		$résultat = [
			"id" => "bob",
			"username" => "bob",
			"rôle" => 0,
			"links" => [
				"self" => "https://example.com/user/bob",
			],
		];

		$userTransformer = new UserTransformer();
		$this->assertEquals($résultat, $userTransformer->transform($user));
	}

	public function test_étant_donné_un_user_avec_ses_clés_lorsquon_inclut_les_clés_on_reçoit_un_tableau_de_clés()
	{
		$user = new User("bob");
		$user->clés = [
			new Clé(null, 1624593600, 1624680000, Clé::PORTEE_AUTH),
			new Clé(null, 1624593602, 1624680002, Clé::PORTEE_AUTH),
		];

		$userTransformer = new UserTransformer();
		$résultats_obtenus = $userTransformer->includeCles($user);

		$clés = [];
		foreach ($résultats_obtenus->getData() as $résultat) {
			$clés[] = $résultat;
		}
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/userTransformerTest_inclusion_clés.json",
			json_encode($clés),
		);
	}
}
