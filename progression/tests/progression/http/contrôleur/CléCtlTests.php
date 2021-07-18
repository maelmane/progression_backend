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

use progression\dao\DAOFactory;
use progression\domaine\entité\{Clé, User};
use Illuminate\Auth\GenericUser;

final class CléCtlTests extends TestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = new GenericUser(["username" => "jdoe", "rôle" => User::ROLE_NORMAL]);
		$this->admin = new GenericUser(["username" => "admin", "rôle" => User::ROLE_ADMIN]);

		// UserDAO
		$mockUserDAO = Mockery::mock("progression\dao\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("jdoe")
			->andReturn(new User("jdoe"));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("bob")
			->andReturn(new User("bob"));

		//CléDAO
		$mockCléDAO = Mockery::mock("progression\dao\CléDAO");
		$mockCléDAO
			->shouldReceive("get_clé")
			->with("jdoe", "clé de test")
			->andReturn(new Clé(null, 1625709495, 1625713000, Clé::PORTEE_AUTH));
		$mockCléDAO
			->shouldReceive("get_clé")
			->with("jdoe", "clé inexistante")
			->andReturn(null);
		$mockCléDAO
			->shouldReceive("get_clé")
			->with("jdoe", "nouvelle clé")
			->andReturn(null);
		$mockCléDAO
			->shouldReceive("save")
			->withArgs(["jdoe", "nouvelle clé", Mockery::Any()])
			->andReturnArg(2);

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory->shouldReceive("get_clé_dao")->andReturn($mockCléDAO);
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	// GET
	public function test_étant_donné_une_clé_existante_et_un_utilisateur_normal_connecté_lorsquon_demande_la_clé_par_nom_on_obtient_un_objet_clé_sans_secret()
	{
		$résultat_observé = $this->actingAs($this->user)->call("GET", "/clé/jdoe/clé%20de%20test/");

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals(
			'{"data":{"type":"cle","id":"jdoe\/clé de test","attributes":{"secret":null,"création":1625709495,"expiration":1625713000,"portée":1},"links":{"self":"https:\/\/example.com\/cle\/jdoe\/clé de test"}}}',
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_normal_connecté_lorsquon_demande_une_clé_inexistante_on_obtient_une_erreur_404()
	{
		$résultat_observé = $this->actingAs($this->user)->call("GET", "/clé/jdoe/clé%20inexistante/");

		$this->assertEquals(404, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Ressource non trouvée."}', $résultat_observé->getContent());
	}

	public function test_étant_donné_un_utilisateur_normal_connecté_lorsquon_demande_une_clé_pour_un_autre_utilisateur_on_obtient_une_erreur_403()
	{
		$résultat_observé = $this->actingAs($this->user)->call("GET", "/clé/bob/une%20clé/");

		$this->assertEquals(403, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Opération interdite."}', $résultat_observé->getContent());
	}

	public function test_étant_donné_un_utilisateur_admin_connecté_lorsquon_demande_une_clé_pour_un_autre_utilisateur_on_obtient_un_objet_clé_sans_secret()
	{
		$résultat_observé = $this->actingAs($this->admin)->call("GET", "/clé/jdoe/clé%20de%20test/");

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertEquals(
			'{"data":{"type":"cle","id":"jdoe\/clé de test","attributes":{"secret":null,"création":1625709495,"expiration":1625713000,"portée":1},"links":{"self":"https:\/\/example.com\/cle\/jdoe\/clé de test"}}}',
			$résultat_observé->getContent(),
		);
	}

	// POST
	public function test_étant_donné_un_utilisateur_normal_connecté_lorsquil_requiert_une_clé_dauthentification_on_obtient_une_clé_générée_aléatoirement()
	{
		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/jdoe/clés", ["nom" => "nouvelle clé"]);

		$this->assertEquals(200, $résultat_observé->status());
		$clé_sauvegardée = json_decode($résultat_observé->getContent())->data->attributes;

		$this->assertNotNull($clé_sauvegardée->secret);
		$this->assertEquals(0, $clé_sauvegardée->expiration);
		$this->assertEquals(Clé::PORTEE_AUTH, $clé_sauvegardée->portée);
	}

	public function test_étant_donné_un_utilisateur_normal_connecté_lorsquon_requiert_une_clé_dauthentification_pour_un_autre_utilisateur_on_obtient_une_erreur_403()
	{
		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/bob/clés", ["nom" => "nouvelle clé"]);

		$this->assertEquals(403, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Opération interdite."}', $résultat_observé->getContent());
	}

	public function test_étant_donné_un_utilisateur_admin_connecté_lorsquil_requiert_une_clé_dauthentification_pour_un_autre_utilisateur_on_obtient_une_clé_générée_aléatoirement()
	{
		$résultat_observé = $this->actingAs($this->user)->call("POST", "/user/jdoe/clés", ["nom" => "nouvelle clé"]);

		$this->assertEquals(200, $résultat_observé->status());
	}
}
