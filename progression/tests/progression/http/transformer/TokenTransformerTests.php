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

use PHPUnit\Framework\TestCase;
use progression\http\transformer\dto\GénériqueDTO;

final class TokenTransformerTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		putenv("APP_URL=https://example.com");
		putenv("APP_VERSION=1.2.3");
		putenv("JWT_SECRET=secret");
	}

	public function tests_étant_donné_un_token_valide_lorsquon_le_transforme_on_obtient_le_tableau_équivalent()
	{
		$token =
			"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6InV0aWxpc2F0ZXVyX2xhbWJkYSIsImN1cnJlbnQiOjk5MDQ0NjQwMCwiZXhwaXJlZCI6MTY4NTgzMTM0MCwicmVzc291cmNlcyI6InJlc3NvdXJjZXMiLCJ2ZXJzaW9uIjoiMS4yLjMifQ.NR4TnTAp-LXZZDGM8EWhSFFQpSda19nULaca9hGcGXI";

		$tokenTransformer = new TokenTransformer("utilisateur_lambda");
		$résultats_obtenus = $tokenTransformer->transform(
			new GénériqueDTO(
				id: "utilisateur_lambda/NR4TnTAp-LXZZDGM8EWhSFFQpSda19nULaca9hGcGXI",
				objet: $token,
				liens: [],
			),
		);
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/token_ressources_avec_expiration.json",
			json_encode($résultats_obtenus),
		);
	}
}
