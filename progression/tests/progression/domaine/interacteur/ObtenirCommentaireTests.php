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

use progression\domaine\entité\Commentaire;
use progression\dao\DAOFactory;
use PHPUnit\Framework\TestCase;
use Mockery;

final class ObtenirCommentaireTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$commentaire = new Commentaire(1234, "il faut déclarer la variable avant de l'instancier ", "John", "2021-06-26 00:00:00");

		$mockCommentaireDAO = Mockery::mock("progression\\dao\\CommentaireDAO");
		$mockCommentaireDAO
			->shouldReceive("get_Commentaire")
			->with("jdoe", 1234)
			->andReturn($commentaire);
		$mockCommentaireDAO
			->shouldReceive("get_Commentaire")
			->with("jdoe", 9999)
			->andReturn(null);

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_Commentaire_dao()
			->andReturn($mockCommentaireDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
		DAOFactory::setInstance(null);
	}

	public function test_étant_donné_un_Commentaire_existante_lorsquon_la_recherche_par_numéro_on_obtient_un_objet_Commentaire_correspondant()
	{
		$int = new ObtenirCommentaireInt();

		$this->assertEquals(
			new Commentaire(null, "2021-06-25 00:00:00", "2021-06-26 00:00:00", 1),
			$int->get_Commentaire("jdoe", 1234),
		);
	}

	public function test_étant_donné_une_Commentaire_inexistante_lorsquon_la_recherche_par_username_et_numéro_on_obtient_null()
	{
		$int = new ObtenirCommentaireInt();

		$this->assertNull($int->get_Commentaire("jdoe", 9999));
	}
}
