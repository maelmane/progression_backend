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

use progression\domaine\entité\Clé;
use progression\dao\DAOFactory;
use PHPUnit\Framework\TestCase;
use Mockery;

final class GénérerCléAuthentificationIntTests extends TestCase
{
	public function setUp(): void
	{
		$mockCléDAO = Mockery::mock("progression\\dao\\CléDAO");
		$mockCléDAO
			->allows("get_clé")
			->with("jdoe", "clé existante")
			->andReturn(
				new Clé(
					null,
					(new \DateTime())->getTimestamp(),
					(new \DateTime())->getTimestamp() + 1,
					Clé::PORTEE_AUTH,
				),
			);
		$mockCléDAO->allows("get_clé")->andReturn(null);

		$mockDAOFactory = Mockery::mock("progression\\dao\\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_clé_dao()
			->andReturn($mockCléDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
		DAOFactory::setInstance(null);
	}

	public function test_étant_donné_un_utilisateur_jdoe_lorsquon_génère_une_clé_d_authentification_une_nouvelle_clé_est_sauvegardée()
	{
		$mockCléDAO = DAOFactory::getInstance()->get_clé_dao();

		$mockCléDAO
			->shouldReceive("save")
			->once()
			->withArgs(function ($username, $nom, $clé) {
				return $username == "jdoe" &&
					$nom == "nouvelle clé" &&
					$clé->création - (new \DateTime())->getTimestamp() < 1 &&
					$clé->expiration == 0 &&
					$clé->portée == Clé::PORTEE_AUTH;
			})
			->andReturnArg(2);

		$résultat_obtenu = (new GénérerCléAuthentificationInt())->générer_clé("jdoe", "nouvelle clé");

		$this->assertNotNull($résultat_obtenu);
	}

	public function test_étant_donné_un_utilisateur_jdoe_lorsquon_génère_deux_clés_d_authentification_elles_ont_des_numéros_différents()
	{
		$mockCléDAO = DAOFactory::getInstance()->get_clé_dao();

		$mockCléDAO
			->allows("save")
			->with("jdoe", Mockery::Any(), Mockery::Any())
			->andReturnArg(2);

		$clé1 = (new GénérerCléAuthentificationInt())->générer_clé("jdoe", "clé 1");
		$clé2 = (new GénérerCléAuthentificationInt())->générer_clé("jdoe", "clé 2");

		$this->assertNotEquals($clé1->secret, $clé2->secret);
	}

	public function test_étant_donné_un_utilisateur_jdoe_lorsquon_génère_une_deuxième_clé_d_authentification_avec_le_même_nom_on_obtient_null()
	{
		$mockCléDAO = DAOFactory::getInstance()->get_clé_dao();

		$clé = (new GénérerCléAuthentificationInt())->générer_clé("jdoe", "clé existante");

		$this->assertNull($clé);
	}

	public function test_étant_donné_un_utilisateur_jdoe_lorsquon_génère_une_clé_d_authentification_sans_nom_on_obtient_null()
	{
		$mockCléDAO = DAOFactory::getInstance()->get_clé_dao();

		$clé = (new GénérerCléAuthentificationInt())->générer_clé("jdoe", "");

		$this->assertNull($clé);
	}
}
