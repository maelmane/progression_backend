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

		$commentaire = new Commentaire(1234, "il faut déclarer la variable avant de l'instancier ", "John", "2021-06-26 00:00:00");

	}

	public function tearDown(): void
	{
		Mockery::close();
		DAOFactory::setInstance(null);
	}

}
