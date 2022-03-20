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

	public function test_étant_donné_un_Commentaire_existante_lorsquon_la_recherche_par_numéro_on_obtient_un_objet_Commentaire_correspondant()
	{
		$commentaireAttendu = new ObtenirCommentaire();
		$commentaire = new Commentaire( 3,"le 3er message","Stefany",1615696276,14);
		$this->assertEquals(
			$commentaire,
			$commentaireAttendu->get_commentaire_par_id(3),
		);
	}

	public function test_étant_donné_une_Commentaire_lorsquon_le_compare_a_un_autre_commentaire_on_obtient_null()
	{
		$commentaireAttendu = new ObtenirCommentaire();

		$this->assertNotEquals($commentaireAttendu->get_commentaire_par_id(3),new Commentaire(  3,"le 2er message","Stefany",1615696276,14));
	}
	public function test_étant_donné_des_Commentaires_existante_lorsquon_la_recherche_par_tentative_on_obtient_un_liste_de_Commentaires_correspondant()
	{
		$commentaireAttendu = new ObtenirCommentaire();
		
		$commentaire = new Commentaire( 1,"le 1er message","jdoe",1615696276,14);
		$commentaire2 = new Commentaire( 2,"le 2er message","admin",1615696276,14);
		$commentaire3 = new Commentaire( 3,"le 3er message","Stefany",1615696276,14);
		$commentaires = array($commentaire,$commentaire2,$commentaire3);
		$this->assertEquals(
			$commentaires,
			$commentaireAttendu->get_commentaire_par_tentative("bob","https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",1615696276),
		);
	}

}
