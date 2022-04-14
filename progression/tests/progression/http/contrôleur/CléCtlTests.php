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

use progression\dao\DAOFactory;
use progression\domaine\entité\{Clé, User};
use Illuminate\Auth\GenericUser;

final class CléCtlTests extends TestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();

		\Gate::define("acces-ressource", function () {
			return true;
		});

		\Gate::define("acces-utilisateur", function () {
			return true;
		});

		$this->user = new GenericUser(["username" => "jdoe", "rôle" => User::ROLE_NORMAL]);
		$this->admin = new GenericUser(["username" => "admin", "rôle" => User::ROLE_ADMIN]);

		// UserDAO
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("jdoe")
			->andReturn(new User("jdoe"));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("bob")
			->andReturn(new User("bob"));

		//CléDAO
		$mockCléDAO = Mockery::mock("progression\\dao\\CléDAO");
		$mockCléDAO
			->shouldReceive("get_clé")
			->with("jdoe", "cle de test")
			->andReturn(new Clé(1234, 1625709495, 1625713000, Clé::PORTEE_AUTH));
		$mockCléDAO
			->shouldReceive("get_clé")
			->with("jdoe", "cle inexistante")
			->andReturn(null);
		$mockCléDAO
			->shouldReceive("get_clé")
			->with("jdoe", "nouvelle cle")
			->andReturn(null);
		$mockCléDAO
			->shouldReceive("save")
			->withArgs(["jdoe", "nouvelle cle", Mockery::Any()])
			->andReturnArg(2);

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_clé_dao")->andReturn($mockCléDAO);
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
		DAOFactory::setInstance(null);
	}

	// GET
	public function test_étant_donné_une_clé_existante_et_un_utilisateur_normal_connecté_lorsquon_demande_la_clé_par_nom_on_obtient_un_objet_clé_sans_secret()
	{
		$résultat_observé = $this->actingAs($this->user)->call("GET", "/cle/jdoe/cle%20de%20test/");

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals(
			'{"data":{"type":"cle","id":"jdoe\/cle de test","attributes":{"secret":null,"création":1625709495,"expiration":1625713000,"portée":1},"links":{"self":"https:\/\/example.com\/cle\/jdoe\/cle de test"}}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_normal_connecté_lorsquon_demande_une_clé_inexistante_on_obtient_une_erreur_404()
	{
		$résultat_observé = $this->actingAs($this->user)->call("GET", "/cle/jdoe/cle%20inexistante/");

		$this->assertEquals(404, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Ressource non trouvée."}', $résultat_observé->getContent());
	}

	public function test_étant_donné_un_utilisateur_admin_connecté_lorsquon_demande_une_clé_pour_un_autre_utilisateur_on_obtient_un_objet_clé_sans_secret()
	{
		$résultat_observé = $this->actingAs($this->admin)->call("GET", "/cle/jdoe/cle%20de%20test/");

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals(
			'{"data":{"type":"cle","id":"jdoe\/cle de test","attributes":{"secret":null,"création":1625709495,"expiration":1625713000,"portée":1},"links":{"self":"https:\/\/example.com\/cle\/jdoe\/cle de test"}}}',
			$résultat_observé->getContent(),
		);
	}

	// POST
	public function test_étant_donné_un_utilisateur_normal_connecté_lorsquil_requiert_une_clé_dauthentification_on_obtient_une_clé_générée_aléatoirement_sans_expiration()
	{
		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/jdoe/cles", ["nom" => "nouvelle cle"]);

		$this->assertEquals(200, $résultat_observé->status());
		$clé_sauvegardée = json_decode($résultat_observé->getContent())->data->attributes;

		$this->assertNotNull($clé_sauvegardée->secret);
		$this->assertEquals(0, $clé_sauvegardée->expiration);
		$this->assertEquals(Clé::PORTEE_AUTH, $clé_sauvegardée->portée);
	}

	public function test_étant_donné_un_utilisateur_normal_connecté_lorsquil_requiert_une_clé_dauthentification_avec_expiration_0_on_obtient_une_clé_générée_aléatoirement_sans_expiration()
	{
		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/jdoe/cles", [
			"nom" => "nouvelle cle",
			"expiration" => 0,
		]);

		$this->assertEquals(200, $résultat_observé->status());
		$clé_sauvegardée = json_decode($résultat_observé->getContent())->data->attributes;

		$this->assertNotNull($clé_sauvegardée->secret);
		$this->assertEquals(0, $clé_sauvegardée->expiration);
		$this->assertEquals(Clé::PORTEE_AUTH, $clé_sauvegardée->portée);
	}

	public function test_étant_donné_un_utilisateur_normal_connecté_lorsquil_requiert_une_clé_dauthentification_avec_expiration_on_obtient_une_clé_générée_aléatoirement_avec_expiration()
	{
		$expiration = time() + 100;
		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/jdoe/cles", [
			"nom" => "nouvelle cle",
			"expiration" => $expiration,
		]);

		$this->assertEquals(200, $résultat_observé->status());
		$clé_sauvegardée = json_decode($résultat_observé->getContent())->data->attributes;

		$this->assertNotNull($clé_sauvegardée->secret);
		$this->assertEquals($expiration, $clé_sauvegardée->expiration);
		$this->assertEquals(Clé::PORTEE_AUTH, $clé_sauvegardée->portée);
	}

	public function test_étant_donné_un_utilisateur_normal_connecté_lorsquil_requiert_une_clé_dauthentification_avec_expiration_passée_on_obtient_une_erreur_400()
	{
		$expiration = time() - 100;
		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/jdoe/cles", [
			"nom" => "nouvelle cle",
			"expiration" => $expiration,
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals('{"erreur":{"expiration":["Expiration invalide"]}}', $résultat_observé->getContent());
	}

	public function test_étant_donné_un_utilisateur_normal_connecté_lorsquil_requiert_une_clé_dauthentification_avec_expiration_non_entière_on_obtient_une_erreur_400()
	{
		$expiration = time() + 100.5;
		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/jdoe/cles", [
			"nom" => "nouvelle cle",
			"expiration" => $expiration,
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals('{"erreur":{"expiration":["Expiration invalide"]}}', $résultat_observé->getContent());
	}

	public function test_étant_donné_un_utilisateur_normal_connecté_lorsquil_requiert_une_clé_dauthentification_avec_expiration_non_numérique_on_obtient_une_erreur_400()
	{
		$expiration = "patate";
		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/jdoe/cles", [
			"nom" => "nouvelle cle",
			"expiration" => $expiration,
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertEquals('{"erreur":{"expiration":["Expiration invalide"]}}', $résultat_observé->getContent());
	}
}
