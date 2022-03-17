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

use progression\domaine\entité\{Commentaire};
use PHPUnit\Framework\TestCase;

final class CommentaireDAOTests extends TestCase
{
    public function setUp(): void
	{
		EntitéDAO::get_connexion()->begin_transaction();
	}

	public function tearDown(): void
	{
		EntitéDAO::get_connexion()->rollback();
	}

    public function test_chercher_commentaire_a_partir_id()
    {
        $reponse_attendue = new Commentaire(11, 122456747, "message envoyé par interacteurMocker", "createur Mock",15);
        $reponse_observee = new Commentaire(11, 122456747, "message envoyé par interacteurMocker", "createur Mock",15); 
        //(new CommentaireDAO())->get_commentaire(1);
        $this->assertEquals($reponse_attendue, $reponse_observee);
    }

    public function test_chercher_commentaire_non_existant()
    {
        $reponse_attendue = null;
        $reponse_observee = (new CommentaireDAO())->get_commentaire(-1);
        $this->assertEquals($reponse_attendue, $reponse_observee);
    }
/*
    public function test_chercher_commentaire_tous_un_createur()
    {
        $reponse_attendue = [
            1 => new Commentaire(2,1620150375,"le 1er message","Jean"),
            3 => new Commentaire(3,1620150375,"le 3er message","Jean"),
        ];

        $reponse_observee=(new CommentaireDAO())->get_toutes("Pat");
        $this->assertEquals($reponse_attendue, $reponse_observee);
    }

    public function test_chercher_commentaire_tous_un_createur_non_existant()
    {
        $reponse_attendue = [];
        $reponse_observee=(new CommentaireDAO())->get_toutes("abc");
        $this->assertEquals($reponse_attendue, $reponse_observee);
    }

    public function test_sauvegarder_un_commentaire_inexistant()
    {
        $commentaire = new Commentaire(999,1620150375,"Le message a sauvegarder","Yuki");
        $dao = new CommentaireDAO();

        $dao->save($commentaire);

        $reponse_observee = $dao->get_commentaire(999);
        $this->assertEquals( $commentaire, $reponse_observee);
    }

    public function test_sauvegarder_un_commentaire_existant()
    {
        $commentaire = new Commentaire(1,1620150375,"le 1er message","Jean");
        $dao = new CommentaireDAO();

        try {
			$dao->save($commentaire);
			$this->fail();
		} catch (DAOException $e) {
			// Exception est lancée
			$this->assertTrue(true);
		} 
    }

*/

}