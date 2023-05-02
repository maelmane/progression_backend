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

use progression\dao\DAOFactory;
use progression\domaine\entité\Commentaire;
use progression\domaine\entité\user\{User, Rôle};
use Illuminate\Auth\GenericUser;

final class CommentaireCtlTests extends ContrôleurTestCase
{
	public $user;

	public function setup(): void
	{
		parent::setUp();

		$this->user = new GenericUser([
			"username" => "jdoe",
			"rôle" => Rôle::NORMAL,
		]);

		$_ENV["APP_URL"] = "https://example.com/";

		// Commentaire
		$commentaire = new Commentaire("Bon travail", $this->user, 1615696276, 5);

		$mockCommentaireDAO = Mockery::mock("progression\\dao\\CommentaireDAO");

		$mockCommentaireDAO
			->shouldReceive("get_commentaires_par_tentative")
			->with("jdoe", "prog1/les_fonctions_01/appeler_une_fonction_paramétrée", 1614374490)
			->andReturn($commentaire);

		$mockCommentaireDAO->shouldReceive("save")->andReturn([$commentaire]);
		// DAOFactory
		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory->shouldReceive("get_commentaire_dao")->andReturn($mockCommentaireDAO);

		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
		DAOFactory::setInstance(null);
	}

	public function test_étant_donné_le_username_dun_utilisateur_le_chemin_dune_question_et_le_timestamp_lorsquon_appelle_post_on_obtient_le_commentaire_avec_ses_relations_sous_forme_json()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/tentative/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490/commentaires",
			[
				"message" => "Bon travail",
				"créateur" => "oteur",
				"numéro_ligne" => 5,
			],
		);
		$this->assertEquals(200, $résultat_obtenu->status());

		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/commentaireCtlTest_1.json",
			$résultat_obtenu->getContent(),
		);
	}

	public function test_étant_donné_le_message_dun_commentaire_non_fourni_dans_la_requete_lorsquon_appelle_post_avec_un_commentaire_on_obtient_une_erreur_400()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/tentative/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490/commentaires",
			["créateur" => "oteur", "numéro_ligne" => 5],
		);
		$this->assertEquals(400, $résultat_obtenu->status());
		$this->assertEquals(
			'{"erreur":{"message":["Err: 1004. Le champ message est obligatoire."]}}',
			$résultat_obtenu->getContent(),
		);
	}

	public function test_étant_donné_le_numero_ligne_dun_commentaire_non_entier_dans_la_requete_lorsquon_appelle_post_avec_un_commentaire_on_obtient_une_erreur_400()
	{
		$résultat_obtenu = $this->actingAs($this->user)->call(
			"POST",
			"/tentative/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9uc18wMS9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU/1614374490/commentaires",
			[
				"message" => "Bon travail",
				"créateur" => "oteur",
				"numéro_ligne" => "numero non entier",
			],
		);
		$this->assertEquals(400, $résultat_obtenu->status());
		$this->assertEquals(
			'{"erreur":{"numéro_ligne":["Err: 1003. Le champ numéro ligne doit être un entier."]}}',
			$résultat_obtenu->getContent(),
		);
	}
}
