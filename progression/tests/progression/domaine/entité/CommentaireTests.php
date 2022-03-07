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

namespace progression\domaine\entité;

use PHPUnit\Framework\TestCase;

final class CommentaireTests extends TestCase
{
	public function test_commentaire_instancié_avec_toutes_les_valeurs_des_attributs_retourne_les_bonnes_valeurs_des_attributs()
	{
		$numéroAttendu = 999;
		$dateAttendu = 20220307;
        $messageAttendu = "Un nouveau message attendu.";
        $créateurAttendu = "Nouveau Créateur";

        $commentaire = new Commentaire($numéroAttendu, $dateAttendu, $messageAttendu, $créateurAttendu);

		$this->assertEquals($numéroAttendu, $commentaire->numéro);
		$this->assertEquals($dateAttendu, $commentaire->date);
		$this->assertEquals($messageAttendu, $commentaire->message);
		$this->assertEquals($créateurAttendu, $commentaire->créateur);
        //$this->assertEquals($créateurAttendu, "Un mauvais créateur qui fait échouer le test");
	}
}