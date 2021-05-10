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

final class AvancementTests extends TestCase
{
	public function test_étant_donné_un_Avancement_instancié_avec_tous_ses_paramètres_lorsquon_récupère_ses_attributs_on_obtient_des_valeurs_identiques()
	{
		$etat_attendu = Question::ETAT_REUSSI;
		$type_attendu = Question::TYPE_PROG;
		$tentatives_attendu = ["exemple_tentative"];

		$résultat_obtenu = new Avancement(["exemple_tentative"], [], $etat_attendu, $type_attendu);

		$this->assertEquals($type_attendu, $résultat_obtenu->type);
		$this->assertEquals($tentatives_attendu, $résultat_obtenu->tentatives);
		$this->assertEquals($etat_attendu, $résultat_obtenu->etat);
	}

	public function test_étant_donné_un_AvancementProg_instancié_sans_tentatives_lorsquon_récupère_ses_tentatives_on_obtient_un_tableau_vide()
	{
		$etat_attendu = Question::ETAT_DEBUT;
		$type_attendu = Question::TYPE_INCONNU;
		$résultat_attendu = [];
		$résultat_obtenu = (new Avancement())->tentatives;

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}
}
