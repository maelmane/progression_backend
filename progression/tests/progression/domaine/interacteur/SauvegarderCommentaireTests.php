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

final class SauvegarderCommentaireTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$commentaire = new Commentaire(11, 122456747, "message envoyer poar interacteurMocker", "createur Mock",15);

	}



	public function test_étant_donné_un_Commentaire_existante_lorsquon_le_sauvegarde_obtient_un_Commentaire_sauvegarder_dans_la_BD()
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
}
