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
use progression\domaine\entité\{Sauvegarde, User};
use Illuminate\Auth\GenericUser;

final class SauvegardeCtlTests extends TestCase
{
	public $user;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = new GenericUser(["username" => "jdoe", "rôle" => User::ROLE_NORMAL]);

		$_ENV["APP_URL"] = "https://example.com/";

		// Sauvegarde
		$sauvegarde = new Sauvegarde(1620150294, "print(\"Hello world!\")");
		$sauvegardes = [];
		$sauvegardes["python"] = new Sauvegarde(1620150294,	"print(\"Hello world!\")");

		$mockSauvegardeDAO = Mockery::mock("progression\dao\SauvegardeDAO");
		$mockSauvegardeDAO
			->shouldReceive("get_sauvegarde")
			->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", "python")
			->andReturn($sauvegarde);
		$mockSauvegardeDAO
			->shouldReceive("get_toutes")
			->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn($sauvegardes);
		$mockSauvegardeDAO
			->shouldReceive("get_sauvegarde")
			->with("jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", "java")
			->andReturn(null);
		$mockSauvegardeDAO
			->shouldReceive("get_toutes")
			->with("bobert", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction")
			->andReturn([]);
		$mockSauvegardeDAO
			->shouldReceive("save")
			->andReturn($sauvegarde);

		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory->shouldReceive("get_sauvegarde_dao")->andReturn($mockSauvegardeDAO);

		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	// GET
	public function test_étant_donné_un_username_existant_luri_dune_question_existante_et_un_langage_existant_lorsquon_appelle_getSauvegarde_on_obtient_une_sauvegarde()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/sauvegarde/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/python",
		);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertStringEqualsFile(
			__DIR__ . "/résultats_attendus/sauvegardeCtlTests_1.json",
			$résultat_observé->getContent(),
		);
	}
	public function test_étant_donné_un_username_existant_luri_dune_question_existante_lorsquon_appelle_getToutes_on_obtient_un_tableau_de_sauvegardes()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/sauvegardes",
		);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertStringEqualsFile(
			__DIR__ . "/résultats_attendus/sauvegardeCtlTests_2.json",
			$résultat_observé->getContent(),
		);
	}
	public function test_étant_donné_un_username_inexistant_lorsquon_appelle_getToutes_on_obtient_un_message_derrreur()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/avancement/bobert/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/sauvegardes",
		);

		$this->assertEquals(404, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Ressource non trouvée."}', $résultat_observé->getContent());
	}
	public function test_étant_donné_un_username_existant_luri_dune_question_existante_et_un_langage_inexistant_lorsquon_appelle_getSauvegarde_on_obtient_un_message_derrreur()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"GET",
			"/sauvegarde/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/java",
		);

		$this->assertEquals(404, $résultat_observé->status());
		$this->assertEquals('{"erreur":"Ressource non trouvée."}', $résultat_observé->getContent());
	}

	// POST
	public function test_étant_donné_le_langage_inexistant_dans_le_corps_de_la_requete_lorsquon_appelle_postSauvegarde_on_obtient_un_message_derrreur()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"POST",
			"/sauvegarde/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			[
				"code" => "print(\"Hello world!\")",
			],
		);

		$this->assertEquals(422, $résultat_observé->status());
		$this->assertEquals('{"erreur":{"langage":["Le champ langage est obligatoire."]}}', $résultat_observé->getContent());
	}
	public function test_étant_donné_le_langage_inexistant_dans_le_corps_de_la_requete_lorsquon_appelle_post_on_obtient_un_message_derrreur()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/sauvegardes",
			[
				"code" => "print(\"Hello world!\")",
			],
		);

		$this->assertEquals(422, $résultat_observé->status());
		$this->assertEquals('{"erreur":{"langage":["Le champ langage est obligatoire."]}}', $résultat_observé->getContent());
	}
	public function test_étant_donné_le_code_inexistant_dans_le_corps_de_la_requete_lorsquon_appelle_postSauvegarde_on_obtient_un_message_derrreur()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"POST",
			"/sauvegarde/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			[
				"langage" => "python"
			]
		);

		$this->assertEquals(422, $résultat_observé->status());
		$this->assertEquals('{"erreur":{"code":["Le champ code est obligatoire."]}}', $résultat_observé->getContent());
	}
	public function test_étant_donné_le_code_inexistant_dans_le_corps_de_la_requete_lorsquon_appelle_post_on_obtient_un_message_derrreur()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/sauvegardes",
			[
				"langage" => "python"
			]
		);

		$this->assertEquals(422, $résultat_observé->status());
		$this->assertEquals('{"erreur":{"code":["Le champ code est obligatoire."]}}', $résultat_observé->getContent());
	}
	public function test_étant_donné_un_username_existant_luri_dune_question_existante_le_code_et_le_langage_existants_dans_le_corps_de_la_requete_lorsquon_appelle_postSauvegarde_on_obtient_une_sauvegarde_nouvellement_créee()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"POST",
			"/sauvegarde/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24",
			[
				"langage" => "python",
				"code" => "print(\"Hello world!\")"
			]
		);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertStringEqualsFile(
			__DIR__ . "/résultats_attendus/sauvegardeCtlTests_1.json",
			$résultat_observé->getContent(),
		);
	}
	public function test_étant_donné_un_username_existant_luri_dune_question_existante_le_code_et_le_langage_existants_dans_le_corps_de_la_requete_lorsquon_appelle_post_on_obtient_une_sauvegarde_nouvellement_créee()
	{
		$résultat_observé = $this->actingAs($this->user)->call(
			"POST",
			"/avancement/jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24/sauvegardes",
			[
				"langage" => "python",
				"code" => "print(\"Hello world!\")"
			]
		);

		$this->assertEquals(200, $résultat_observé->status());
		$this->assertStringEqualsFile(
			__DIR__ . "/résultats_attendus/sauvegardeCtlTests_1.json",
			$résultat_observé->getContent(),
		);
	}
}
