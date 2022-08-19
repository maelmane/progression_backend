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

use progression\TestCase;

final class EntitéDAOTests extends TestCase
{
	public function test_étant_donné_des_includes_sans_hiérarchie_lorsquon_filtre_les_niveaux_on_obtient_tous_sauf_le_niveau_actuel()
	{
		$niveaux = ["niveau1", "niveau2", "niveau3"];

		$résultats_attendus = ["niveau1", "niveau3"];

		$résultats_obtenus = EntitéDAO::filtrer_niveaux($niveaux, "niveau2");

		$this->assertEquals($résultats_attendus, $résultats_obtenus);
	}

	public function test_étant_donné_des_includes_hiérarchisés_lorsquon_filtre_les_niveaux_on_obtient_seulement_les_sous_niveaux()
	{
		$niveaux = ["niveau0", "niveau0.niveau1", "niveau0.niveau2", "niveau0.niveau1.niveau3"];

		$résultats_attendus = ["niveau1", "niveau2", "niveau1.niveau3"];

		$résultats_obtenus = EntitéDAO::filtrer_niveaux($niveaux, "niveau0");

		$this->assertEquals($résultats_attendus, $résultats_obtenus);
	}
}
