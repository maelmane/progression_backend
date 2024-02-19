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

namespace progression\dao;

use progression\domaine\entité\clé\{Clé, Portée};
use progression\TestCase;
use progression\domaine\interacteur\IntégritéException;

final class CléDAOTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();
		app("db")->connection()->beginTransaction();
	}

	public function tearDown(): void
	{
		app("db")->connection()->rollBack();
		parent::tearDown();
	}

	public function test_étant_donné_une_clé_existante_lorsquon_la_récupère_on_obtient_ses_attributs()
	{
		$dao = new CléDAO();
		$clé = $dao->get_clé("bob", "clé de test");

		$résultat_attendu = new Clé(null, 1624593600, 1624680000, Portée::AUTH);
		$this->assertEquals($résultat_attendu, $clé);
	}

	public function test_étant_donné_deux_clés_existantes_lorsquon_les_récupère_toutes_on_obtient_deux_clés()
	{
		$dao = new CléDAO();
		$clés = $dao->get_toutes("bob");

		$résultat_attendu = [
			"clé de test" => new Clé(null, 1624593600, 1624680000, Portée::AUTH),
			"clé de test 2" => new Clé(null, 1624593602, 1624680002, Portée::AUTH),
		];
		$this->assertEquals($résultat_attendu, $clés);
	}

	public function test_étant_un_utilisateur_sans_clé_lorsquon_les_récupère_toutes_on_obtient_un_tableau_vide()
	{
		$dao = new CléDAO();
		$clés = $dao->get_toutes("jdoe");

		$résultat_attendu = [];
		$this->assertEquals($résultat_attendu, $clés);
	}

	public function test_étant_donné_une_clé_inexistante_lorsquon_la_sauvegarde_on_obtient_une_clé_avec_son_secret()
	{
		$clé = new Clé("secret", 1624593600, 1624680000);
		$résultat_attendu = $clé;

		$dao = new CléDAO();
		$résultat_obtenu = $dao->save("bob", "nouvelle clé", $clé);
		$this->assertEquals(["nouvelle clé" => $résultat_attendu], $résultat_obtenu);
	}

	public function test_étant_donné_une_clé_inexistante_lorsquon_la_sauvegarde_on_la_retrouve_dans_la_bd_sans_son_secret()
	{
		$clé = new Clé("secret", 1624593600, 1624680000);

		$dao = new CléDAO();
		$dao->save("bob", "nouvelle clé", $clé);

		$résultat_attendu = new Clé(null, 1624593600, 1624680000, Portée::AUTH);
		$résultat_obtenu = $dao->get_clé("bob", "nouvelle clé");

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

	public function test_étant_donné_une_clé_existante_lorsquon_la_vérifie_en_donnant_le_bon_secret_on_obtient_vrai()
	{
		$clé = new Clé("secret", 1624593600, 1624680000, Portée::AUTH);

		$dao = new CléDAO();
		$dao->save("bob", "nouvelle clé", $clé);

		$this->assertTrue($dao->vérifier("bob", "nouvelle clé", "secret"));
	}

	public function test_étant_donné_une_clé_existante_lorsquon_la_vérifie_en_donnant_le_mauvais_secret_on_obtient_faux()
	{
		$clé = new Clé("secret", 1624593600, 1624680000, Portée::AUTH);

		$dao = new CléDAO();
		$dao->save("bob", "nouvelle clé", $clé);

		$this->assertFalse($dao->vérifier("bob", "nouvelle clé", "9999"));
	}

	public function test_étant_donné_une_clé_existante_lorsquon_la_sauvegarde_de_nouveau_on_obtient_une_exception()
	{
		$clé = new Clé(1234, 1624593600, 1624680000, Portée::AUTH);

		$dao = new CléDAO();
		try {
			$dao->save("bob", "clé de test", $clé);
			$this->fail();
		} catch (DAOException $e) {
			// Exception attendue
			$this->assertTrue(true);
		}
	}
}
