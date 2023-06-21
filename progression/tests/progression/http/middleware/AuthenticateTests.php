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
use progression\domaine\entité\question\QuestionProg;
use progression\domaine\entité\clé\{Clé, Portée};
use progression\domaine\entité\user\{User, Rôle, État};
use progression\dao\DAOFactory;
use Illuminate\Auth\GenericUser;
use Carbon\Carbon;

final class AuthenticateTests extends TestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();

		$this->user = new GenericUser([
			"username" => "bob",
			"rôle" => Rôle::NORMAL,
			"état" => État::ACTIF,
		]);

		putenv("APP_URL=https://example.com/");
		putenv("JWT_SECRET=secret");
		putenv("JWT_TTL=86400");
		putenv("APP_VERSION=1.2.3");

		Carbon::setTestNowAndTimezone(Carbon::create(2001, 5, 21, 12));

		// UserDAO
		$mockUserDAO = Mockery::mock("progression\\dao\\UserDAO");
		$mockUserDAO
			->shouldReceive("get_user")
			->with("bob")
			->andReturn(new User(username: "bob", date_inscription: 0, état: État::ACTIF));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("bob", [])
			->andReturn(new User(username: "bob", date_inscription: 0, état: État::ACTIF));
		$mockUserDAO
			->shouldReceive("trouver")
			->with(null, "bob@progressionmail.com", [])
			->andReturn(new User(username: "bob", date_inscription: 0, état: État::ACTIF));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("roger")
			->andReturn(new User(username: "roger", date_inscription: 0, état: État::INACTIF));
		$mockUserDAO
			->shouldReceive("get_user")
			->with("marcel")
			->andReturn(new User(username: "marcel", date_inscription: 0, état: État::ATTENTE_DE_VALIDATION));
		$mockUserDAO->shouldReceive("get_user")->andReturn(null);

		$mockUserDAO
			->shouldReceive("vérifier_password")
			->with(Mockery::Any(), "incorrect")
			->andReturn(false);
		$mockUserDAO
			->shouldReceive("vérifier_password")
			->with(Mockery::Any(), "m0tD3P4ZZE")
			->andReturn(true);

		// CléDAO
		$mockCléDAO = Mockery::mock("progression\\dao\\CléDAO");
		$mockCléDAO
			->shouldReceive("get_clé")
			->with("bob", "cleValide01")
			->andReturn(new Clé(null, (new \DateTime())->getTimestamp(), 0, Portée::AUTH));
		$mockCléDAO
			->shouldReceive("vérifier")
			->with("bob", "cleValide01", "secret")
			->andReturn(true);
		$mockCléDAO->shouldReceive("get_clé")->andReturn(null);
		$mockCléDAO->shouldReceive("vérifier")->andReturn(false);

		$mockQuestionDAO = Mockery::mock("progression\\dao\\QuestionDAO");
		$mockQuestionDAO
			->shouldReceive("get_question")
			->with("question_de_test")
			->andReturn(new QuestionProg());

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_user_dao")->andReturn($mockUserDAO);
		$mockDAOFactory->shouldReceive("get_clé_dao")->andReturn($mockCléDAO);
		$mockDAOFactory->shouldReceive("get_question_dao")->andReturn($mockQuestionDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
		GénérateurDeToken::set_instance(null);
	}

	#  AUTH LDAP
	# Ces tests nécessitent que LDAP soit découplé de l'interacteur: https://git.dti.crosemont.quebec/progression/progression_backend/-/issues/79
	//public function test_étant_donné_un_utilisateur_existant_et_une_authentification_LDAP_effectue_une_requête_avec_identifiant_et_mdp_corrects_on_obtient_la_ressource()
	//{
	//	putenv("AUTH_LOCAL=false");
	//	putenv("AUTH_LDAP=true");
	//
	//	$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
	//		"identifiant" => "bob",
	//		"password" => "m0tD3P4ZZE",
	//	]);
	//
	//    $this->assertEquals(200, $résultat_observé->status());
	//	$this->assertJsonStringEqualsJsonString('{"Token":"token valide"}', $token = $résultat_observé->getContent());
	//}
	//
	//public function test_étant_donné_un_utilisateur_existant_et_une_authentification_LDAP_effectue_une_requête_avec_courriel_et_mdp_corrects_on_obtient_la_ressource()
	//{
	//	putenv("AUTH_LOCAL=false");
	//	putenv("AUTH_LDAP=true");
	//
	//	$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
	//		"identifiant" => "bob@progressionmail.com",
	//		"password" => "m0tD3P4ZZE",
	//	]);
	//
	//	$this->assertEquals(200, $résultat_observé->status());
	//	$this->assertJsonStringEqualsJsonString('{"Token":"token valide"}', $token = $résultat_observé->getContent());
	//}

	public function test_étant_donné_un_utilisateur_inexistant_avec_authentification_LDAP_lorsquon_effectue_une_requête_sans_domaine_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=true");

		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "Marcel",
			"password" => "m0tD3P4ZZE",
		]);

		$this->assertEquals(401, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonString('{"erreur":"Accès interdit."}', $résultat_observé->content());
	}

	public function test_étant_donné_une_authentification_LDAP_lorsquon_effectue_une_requête_avec_un_nom_dutilisateur_invalide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=true");

		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "Marcel@test@ici.com",
			"password" => "password",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonString(
			'{"erreur":{"identifiant":["L\'identifiant doit être un nom d\'utilisateur ou un courriel valide."]}}',
			$résultat_observé->getContent(),
		);
	}

	#  Aucune AUTH
	public function test_étant_donné_un_utilisateur_existant_et_sans_authentification_lorsquon_effectue_une_requête_avec_identifiant_on_obtient_la_ressource()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/question/cXVlc3Rpb25fZGVfdGVzdA", // question_de_test
			[
				"identifiant" => "bob",
			],
		);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/ressource_question_de_test.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_inexistant_sans_authentification_lorsquon_effectue_une_requête_avec_un_courriel_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=false");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "marcel@ici.com",
			"password" => "password",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonString(
			'{"erreur":{"identifiant":["L\'identifiant doit être un nom d\'utilisateur ou un courriel valide."]}}',
			$résultat_observé->getContent(),
		);
	}

	# AUTH locale
	public function test_étant_donné_un_utilisateur_existant_et_une_authentification_locale_lorsquon_effectue_une_requête_avec_identifiant_et_mdp_corrects_on_obtient_la_ressource()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/question/cXVlc3Rpb25fZGVfdGVzdA", // question_de_test
			[
				"identifiant" => "bob",
				"password" => "m0tD3P4ZZE",
			],
		);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/ressource_question_de_test.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_existant_et_une_authentificaton_locale_lorsquon_effectue_une_requête_avec_courriel_et_mdp_corrects_on_obtient_la_ressource()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/question/cXVlc3Rpb25fZGVfdGVzdA", // question_de_test
			[
				"identifiant" => "bob@progressionmail.com",
				"password" => "m0tD3P4ZZE",
			],
		);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/ressource_question_de_test.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_inactif_avec_authentification_lorsquon_effectue_une_requête_mdp_correct_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "roger",
			"password" => "m0tD3P4ZZE",
		]);
		$this->assertEquals(401, $résultat_observé->status());
	}

	public function test_étant_donné_un_utilisateur_en_attente_de_validation_avec_authentification_lorsquon_effectue_une_requête_mdp_correct_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "marcel",
			"password" => "m0tD3P4ZZE",
		]);
		$this->assertEquals(401, $résultat_observé->status());
	}

	public function test_étant_donné_un_utilisateur_inexistant_avec_authentification_lorsquon_effectue_une_requête_avec_identifiant_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "Zozo",
			"password" => "m0tD3P4ZZE",
		]);

		$this->assertEquals(401, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonString('{"erreur":"Accès interdit."}', $résultat_observé->content());
	}

	public function test_étant_donné_un_utilisateur_existant_avec_authentification_effectue_une_requête_avec_identifiant_et_mdp_erroné_on_obtient_une_erreur_401()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "bob",
			"password" => "incorrect",
		]);

		$this->assertEquals(401, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonString('{"erreur":"Accès interdit."}', $résultat_observé->content());
	}

	# Identifiants invalides

	public function test_étant_donné_une_authentificaton_locale_lorsquon_effectue_une_requête_un_nom_dutilisateur_vide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "",
			"password" => "m0tD3P4ZZE",
		]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentificaton_locale_lorsquon_effectue_une_requête_un_nom_dutilisateur_invalide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "bo bo",
			"password" => "m0tD3P4ZZE",
		]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentificaton_locale_lorsquon_effectue_une_requête_sans_nom_dutlisateur_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"password" => "m0tD3P4ZZE",
		]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentification_locale_lorsquon_effectue_une_requête_sans_mot_de_passe_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", ["identifiant" => ""]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_une_authentification_locale_lorsquon_effectue_une_requête_avec_mot_de_passe_vide_on_obtient_une_erreur_400()
	{
		putenv("AUTH_LOCAL=true");
		putenv("AUTH_LDAP=false");

		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "bob",
			"password" => "",
		]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	# Authentification par clé
	public function test_étant_donné_un_utilisateur_existant_et_une_clé_dauthentification_valide_effectue_une_requête_on_obtient_la_ressource()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/question/cXVlc3Rpb25fZGVfdGVzdA", // question_de_test
			[
				"identifiant" => "bob",
				"key_name" => "cleValide01",
				"key_secret" => "secret",
			],
		);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/ressource_question_de_test.json",
			$résultat_observé->getContent(),
		);
	}

	public function test_étant_donné_un_utilisateur_existant_et_une_clé_dauthentification_valide_effectue_une_requête_avec_un_identifiant_invalide_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->actingAs($this->user)
			->actingAs($this->user)
			->call("GET", "/user/bob", [
				"identifiant" => "bob@progressionmail.com",
				"key_name" => "cleValide01",
				"key_secret" => "secret",
			]);

		$this->assertEquals(400, $résultat_observé->status());
	}

	public function test_étant_donné_un_utilisateur_existant_et_une_clé_dauthentification_invalide_effectue_une_requête_on_obtient_une_erreur_401()
	{
		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "bob",
			"key_name" => "cleInvalide00",
			"key_secret" => "secret",
		]);

		$this->assertEquals(401, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonString('{"erreur":"Accès interdit."}', $résultat_observé->content());
	}

	public function test_étant_donné_un_utilisateur_existant_effectue_une_requête_avec_une_clé_vide_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "bob",
			"key_name" => "",
			"key_secret" => "secret",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonString(
			'{"erreur":{"password":["Err: 1004. Le champ password est obligatoire lorsque key_name n\'est pas présent."]}}',
			$résultat_observé->content(),
		);
	}

	public function test_étant_donné_un_utilisateur_existant_effectue_une_requête_avec_une_clé_au_nom_invalide_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "bob",
			"key_name" => "tata toto",
			"key_secret" => "secret",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonString(
			'{"erreur":{"key_name":["Err: 1003. Le champ key_name doit être alphanumérique \'a-Z0-9-_\'"]}}',
			$résultat_observé->content(),
		);
	}

	public function test_étant_donné_un_utilisateur_existant_effectue_une_requête_sans_clé_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "bob",
			"key_secret" => "secret",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonString(
			'{"erreur":{"password":["Err: 1004. Le champ password est obligatoire lorsque key_name n\'est pas présent."]}}',
			$résultat_observé->content(),
		);
	}

	public function test_étant_donné_un_utilisateur_existant_effectue_une_requête_avec_un_secret_vide_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "bob",
			"key_name" => "cleValide01",
			"key_secret" => "",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonString(
			'{"erreur":{"key_secret":["Err: 1004. Le champ key_secret est obligatoire lorsque key_name est présent."]}}',
			$résultat_observé->content(),
		);
	}

	public function test_étant_donné_un_utilisateur_existant_effectue_une_requête_sans_secret_on_obtient_une_erreur_400()
	{
		$résultat_observé = $this->actingAs($this->user)->call("GET", "/user/bob", [
			"identifiant" => "bob",
			"key_name" => "cleValide01",
		]);

		$this->assertEquals(400, $résultat_observé->status());
		$this->assertJsonStringEqualsJsonString(
			'{"erreur":{"key_secret":["Err: 1004. Le champ key_secret est obligatoire lorsque key_name est présent."]}}',
			$résultat_observé->content(),
		);
	}
}
