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
		$commentaire = new Commentaire(11, 122456747, "message envoyer poar interacteurMocker", "createur Mock",15);
	}

	public function test_étant_donné_un_Commentaire_existante_lorsquon_la_recherche_par_numéro_on_obtient_un_objet_Commentaire_correspondant()
	{
		$commentaireAttendu = new ObtenirCommentaire();
		$commentaire = new Commentaire(11, 122456747, "message envoyer poar interacteurMocker", "createur Mock",15);
		$commentaire2 = new Commentaire(13, 1224432747, "message de test", "createur Test",22);
		$commentaires = array($commentaire,$commentaire2);
		$this->assertEquals(
			$commentaires,
			$commentaireAttendu->get_Commentaire(),
		);
	}

	public function test_étant_donné_une_Commentaire_lorsquon_le_compare_a_un_autre_commentaire_on_obtient_null()
	{
		$commentaireAttendu = new ObtenirCommentaire();

		$this->assertNotEquals($commentaireAttendu->get_Commentaire(),new Commentaire(12, 122456747, "message envoyer poar interacteurMocker", "createur Mock",14));
	}
}
