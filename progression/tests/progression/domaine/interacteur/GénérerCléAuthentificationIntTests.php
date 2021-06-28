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
		$mockCléDAO = Mockery::mock("progression\dao\CléDAO");

		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_clé_dao()
			->andReturn($mockCléDAO);
		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		Mockery::close();
	}

	public function test_étant_donné_un_utilisateur_jdoe_lorsquon_génère_une_clé_d_authentification_une_nouvelle_clé_est_sauvegardée()
	{
		$mockCléDAO = DAOFactory::getInstance()->get_clé_dao();

		$mockCléDAO
			->shouldReceive("save")
			->once()
			->withArgs(function ($username, $numéro, $clé) {
				return $username == "jdoe" &&
					$clé->création->getTimestamp() - (new \DateTime())->getTimestamp() < 1 &&
					$clé->expiration == 0 &&
					$clé->portée == Clé::PORTEE_AUTH;
			})
			->andReturnArg(2);

		$résultat_obtenu = (new GénérerCléAuthentificationInt())->générer_clé("jdoe");

		$this->assertNotNull($résultat_obtenu);
	}

	public function test_étant_donné_un_utilisateur_jdoe_lorsquon_génère_deux_clés_d_authentification_elles_ont_des_numéros_différents()
	{
		$mockCléDAO = DAOFactory::getInstance()->get_clé_dao();

		$mockCléDAO
			->allows("save")
			->with("jdoe", Mockery::Any(), Mockery::Any())
			->andReturnArg(2);

		$clé1 = (new GénérerCléAuthentificationInt())->générer_clé("jdoe");
		$clé2 = (new GénérerCléAuthentificationInt())->générer_clé("jdoe");

		$this->assertNotEquals($clé1, $clé2);
	}
}
