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

namespace progression\domaine\interacteur;

use progression\domaine\entité\{TentativeProg, TentativeSys};
use progression\domaine\entité\Commentaire;
use progression\domaine\entité\Résultat;
use progression\dao\DAOFactory;
use progression\dao\tentative\TentativeDAO;
use PHPUnit\Framework\TestCase;
use Mockery;

final class ObtenirTentativeIntTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$mockTentativeDAO = Mockery::mock("progression\\dao\\tentative\\TentativeDAO");
		$mockCommentaireDAO = Mockery::mock("progression\\dao\\CommentaireDAO");
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with("jdoe", "prog1/les_fonctions_01/appeler_une_fonction_paramétrée", 1614711760)
			->andReturn(new TentativeProg("java", "System.out.println();", 1614711760));
		$mockTentativeDAO
			->shouldReceive("get_tentative")
			->with(Mockery::any(), Mockery::any(), Mockery::any())
			->andReturn(null);

		$tentativeSysTest = $mockTentativeDAO
			->shouldReceive("get_dernière")
			->with("jdoe", "https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions")
			->andReturn(
				new TentativeSys(
					(object) ["id" => "conteneurTest2", "ip" => "192.168.0.2", "port" => 12345],
					"reponseTest2",
					3456,
					true,
					[],
					2,
					100,
					"Bravo!",
					[],
				),
			);

		$mockCommentaireDAO
			->shouldReceive("get_commentaires_par_tentative")
			->with("jdoe", "prog1/les_fonctions_01/appeler_une_fonction_paramétrée", 1614711760)
			->andReturn([
				new Commentaire(99, "le 99iem message", "mock", 1615696276, 14),
				new Commentaire(100, "le 100ieme message", "mock", 1615696888, 17),
			]);
		$mockCommentaireDAO
			->shouldReceive("get_commentaires_par_tentative")
			->with(Mockery::any(), Mockery::any(), Mockery::any())
			->andReturn(null);

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_tentative_dao()
			->andReturn($mockTentativeDAO);
		$mockDAOFactory
			->allows()
			->get_commentaire_dao()
			->andReturn($mockCommentaireDAO);

		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_une_tentative_avec_des_attributs_lorsque_cherché_par_user_id_question_id_et_date_soumission_on_obtient_un_objet_tentative_correspondant()
	{
		$interacteur = new ObtenirTentativeInt();

		$résultat_obtenu = $interacteur->get_tentative(
			"jdoe",
			"prog1/les_fonctions_01/appeler_une_fonction_paramétrée",
			1614711760,
		);

		$résultat_attendu = new TentativeProg("java", "System.out.println();", 1614711760);
		$résultat_attendu->commentaires = [
			new Commentaire(99, "le 99iem message", "mock", 1615696276, 14),
			new Commentaire(100, "le 100ieme message", "mock", 1615696888, 17),
		];

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

	public function test_étant_donné_une_tentative_inexistante_lorsque_cherchée_on_obtient_null()
	{
		$interacteur = new ObtenirTentativeInt();
		$résultat_obtenu = $interacteur->get_tentative("patate", "une_question_inexistante", 1614711760);

		$this->assertNull($résultat_obtenu);
	}

	public function test_étant_donné_un_numéro_de_conteneur_inexistant_on_récupère_lid_du_conteneur_de_la_dernière_tentative()
	{
		$interacteur = new ObtenirTentativeInt();
		$résultat_obtenu = $interacteur->get_dernière(
			"jdoe",
			"https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions",
		);

		$this->assertEquals($résultat_obtenu->conteneur->id, "conteneurTest2");
	}
}
