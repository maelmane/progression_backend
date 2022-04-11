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

final class ObtenirCommentaireIntTests extends TestCase
{
	public function test_étant_donné_un_commentaire_existant_lorsquon_le_recherche_par_id_on_obtient_un_objet_commentaire_correspondant()
	{
		$commentaireAttendu = new ObtenirCommentaireInt();
		$commentaire[3] = new Commentaire("le 3er message", "Stefany", 1615696276, 14);
		$réponse_attendue = $commentaire;
		$this->assertEquals($commentaire, $commentaireAttendu->get_commentaire_par_id(3));
	}

	public function test_étant_donné_un_id_inexistant_lorsquon_cherche_son_commentaire_on_obtient_null()
	{
		$commentaireAttendu = new ObtenirCommentaireInt();

		$this->assertNull($commentaireAttendu->get_commentaire_par_id(9999999));
	}
	public function test_étant_donné_des_commentaires_existants_lorsquon_les_recherche_par_tentative_on_obtient_une_liste_de_commentaires_correspondante()
	{
		$commentaireObtenu = new ObtenirCommentaireInt();

		$tableauCommentaire[1] = new Commentaire("le 1er message", "jdoe", 1615696276, 14);
		$tableauCommentaire[2] = new Commentaire("le 2er message", "admin", 1615696276, 12);
		$tableauCommentaire[3] = new Commentaire("le 3er message", "Stefany", 1615696276, 14);
		$réponse_attendue = $tableauCommentaire;
		$this->assertEquals(
			$réponse_attendue,
			$commentaireObtenu->get_commentaires_par_tentative(
				"bob",
				"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
				1615696276,
			),
		);
	}
}
