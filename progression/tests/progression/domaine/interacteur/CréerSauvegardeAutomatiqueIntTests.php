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

use progression\domaine\entité\{Sauvegarde};
use progression\dao\DAOFactory;
use PHPUnit\Framework\TestCase;
use Mockery;

final class CréerSauvegardeAutomatiqueIntTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		// Sauvegarde
		$sauvegarde = new Sauvegarde(
				1620150294,
				"print(\"Hello world!\")"
			);
		$mockSauvegardeDAO = Mockery::mock("progression\dao\SauvegardeDAO");
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

	public function test_étant_donné_luri_dune_question_existante_un_username_existant_et_le_bon_langage_lorsquon_appelle_save_on_obtient_un_objet_sauvegarde_correspondant()
	{
		$sauvegarde = new Sauvegarde(
				1620150294,
				"print(\"Hello world!\")"
			);
		$interacteur = new CréerSauvegardeAutomatiqueInt();
		$résultat_obtenu = $interacteur->sauvegarder($sauvegarde, "jdoe", "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", "python");
		$résultat_attendu = new Sauvegarde(
				1620150294,
				"print(\"Hello world!\")"
			);
		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}
}
