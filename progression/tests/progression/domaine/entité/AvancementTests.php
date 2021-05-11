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
		$sauvegardes_attendues = ["exemple_sauvegarde"];

		$résultat_obtenu = new Avancement(Question::ETAT_REUSSI, Question::TYPE_PROG, ["exemple_tentative"], ["exemple_sauvegarde"]);

		$this->assertEquals($etat_attendu, $résultat_obtenu->etat);
		$this->assertEquals($type_attendu, $résultat_obtenu->type);
		$this->assertEquals($tentatives_attendu, $résultat_obtenu->tentatives);
		$this->assertEquals($sauvegardes_attendues, $résultat_obtenu->sauvegardes);
	}

	public function test_étant_donné_un_AvancementProg_instancié_sans_tentatives_lorsquon_récupère_ses_tentatives_on_obtient_un_tableau_vide()
	{
		$résultat_attendu = [];
		$résultat_obtenu = (new Avancement())->tentatives;

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}

	public function test_étant_donné_un_AvancementProg_instancié_sans_sauvegardes_lorsquon_récupère_ses_sauvegardes_on_obtient_un_tableau_vide()
	{
		$résultat_attendu = [];
		$résultat_obtenu = (new Avancement())->sauvegardes;

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}
}
