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

    public function test_chercher_commentaire_a_partir_id
    {
        $reponse_attendue = new Commentaire{1,"le 1er message","Jean"};
        $reponse_observee = (new CommentaireDAO()->get_commentaire(1));
        $this->assertEquals($réponse_attendue, $résponse_observée);
    }
}