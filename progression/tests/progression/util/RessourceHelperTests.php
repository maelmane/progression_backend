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

namespace progression\util;

use PHPUnit\Framework\TestCase;

final class RessourcesTests extends TestCase
{
	public function test_étant_donné_un_RessourceHelper_instancié_sans_paramètre_lorsquon_récupère_ses_attributs_on_obtient_des_valeurs_par_défaut()
	{
		$urlAttendu = ["*"];
		$methodAttendu = ["*"];

		$résultatObtenu = new RessourceHelper();

		$this->assertEquals($urlAttendu, $résultatObtenu->urlArray);
		$this->assertEquals($methodAttendu, $résultatObtenu->methodArray);
	}

    public function test_étant_donné_une_Ressource_instanciée_avec_des_paramètres_spécifiques_lorsquon_récupère_ses_attributs_on_obtient_des_valeurs_identiques()
	{
		$urlAttendu = ["http://www.exemple.com", "http://www.exemple.qc.ca"];
		$methodAttendu = ["POST", "GET"];

		$résultatObtenu = new RessourceHelper($urlAttendu, $methodAttendu);

        print_r($résultatObtenu->obtenirEnJson());

		$this->assertEquals($urlAttendu, $résultatObtenu->urlArray);
		$this->assertEquals($methodAttendu, $résultatObtenu->methodArray);
	}

    public function test_étant_donné_une_Ressource_instanciée_avec_un_paramètre_en_json_lorsquon_récupère_ses_attributs_on_obtient_des_valeurs_identiques()
	{

		$urlAttendu = ["http://www.exemple.com", "http://www.exemple.qc.ca"];
		$methodAttendu = ["POST", "GET"];

		$résultatObtenu = new RessourceHelper($urlAttendu, $methodAttendu);

		$this->assertEquals($urlAttendu, $résultatObtenu->urlArray);
		$this->assertEquals($methodAttendu, $résultatObtenu->methodArray);
	}
}