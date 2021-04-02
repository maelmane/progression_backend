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

require_once __DIR__ . "/../../../TestCase.php";

use progression\http\middleware\ValidationPermissions;
use progression\domaine\entité\User;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

final class ValidationPermissionsTests extends TestCase
{
	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_un_utilisateur_normal_bob_connecté_lorsquon_demande_une_ressource_pour_ce_même_utilisateur_on_obtient_OK()
	{
		$mockRequest = Mockery::mock("Illuminate\Http\Request");
		$mockRequest
			->allows()
			->get("utilisateurConnecté")
			->andReturn(new User("bob"));
		$mockRequest
			->allows()
			->all()
			->andReturn([
				"username" => "bob",
			]);
		$mockRequest->request = $mockRequest;

		$cobaye = new ValidationPermissions();
		$résultat_obtenu = $cobaye->handle($mockRequest, function () {
			return "OK";
		});

		$this->assertEquals("OK", $résultat_obtenu);
	}

	public function test_étant_donné_un_utilisateur_normal_bob_connecté_lorsquon_demande_une_ressource_pour_l_utilisateur_existant_jdoe_on_obtient_erreur_403()
	{
		$mockRequest = Mockery::mock("Illuminate\Http\Request");
		$mockRequest
			->allows()
			->get("utilisateurConnecté")
			->andReturn(new User("bob"));
		$mockRequest
			->allows()
			->all()
			->andReturn([
				"username" => "jdoe",
			]);
		$mockRequest->request = $mockRequest;

		$cobaye = new ValidationPermissions();
		$résultat_obtenu = $cobaye->handle($mockRequest, function () {
			return "OK";
		});

		$résultat_attendu = [
			"erreur" => "Accès interdit.",
		];
		$this->assertEquals(403, $résultat_obtenu->status());
		$this->assertEquals(
			$résultat_attendu,
			json_decode($résultat_obtenu->getContent(), true)
		);
	}

	public function test_étant_donné_un_utilisateur_normal_bob_connecté_lorsquon_demande_une_ressource_pour_l_utilisateur_inexistant_jdoe_on_obtient_erreur_403()
	{
		$mockRequest = Mockery::mock("Illuminate\Http\Request");
		$mockRequest
			->allows()
			->get("utilisateurConnecté")
			->andReturn(new User("bob"));
		$mockRequest
			->allows()
			->all()
			->andReturn([
				"username" => null,
			]);
		$mockRequest->request = $mockRequest;

		$cobaye = new ValidationPermissions();
		$résultat_obtenu = $cobaye->handle($mockRequest, function () {
			return "OK";
		});

		$résultat_attendu = [
			"erreur" => "Accès interdit.",
		];
		$this->assertEquals(403, $résultat_obtenu->status());
		$this->assertEquals(
			$résultat_attendu,
			json_decode($résultat_obtenu->getContent(), true)
		);
	}
	public function test_étant_donné_un_utilisateur_admin_connecté_lorsquon_demande_une_ressource_pour_l_utilisateur_bob_on_obtient_OK()
	{
		$mockRequest = Mockery::mock("Illuminate\Http\Request");
		$mockRequest
			->allows()
			->get("utilisateurConnecté")
			->andReturn(new User("admin", User::ROLE_ADMIN));
		$mockRequest
			->allows()
			->all()
			->andReturn([
				"username" => "bob",
			]);
		$mockRequest->request = $mockRequest;

		$cobaye = new ValidationPermissions();
		$résultat_obtenu = $cobaye->handle($mockRequest, function () {
			return "OK";
		});

		$this->assertEquals("OK", $résultat_obtenu);
	}
}
