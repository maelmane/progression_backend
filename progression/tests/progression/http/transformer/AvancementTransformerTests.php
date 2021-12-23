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

namespace progression\http\transformer;

use progression\domaine\entité\{Avancement, TentativeProg, Question, Sauvegarde};
use PHPUnit\Framework\TestCase;

final class AvancementTransformerTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		$_ENV["APP_URL"] = "https://example.com/";
	}

	public function test_étant_donné_un_avancement_instancié_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_array_d_objets_identique()
	{
		$avancement = new Avancement(Question::ETAT_DEBUT, Question::TYPE_PROG);
		$avancement->id =
			"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24";

		$avancementTransformer = new AvancementTransformer();
		$résultats_obtenus = $avancementTransformer->transform($avancement);
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/avancementTransformerTest_1.json",
			json_encode($résultats_obtenus),
		);
	}

	public function test_étant_donné_un_avancement_avec_ses_tentatives_lorsquon_inclut_les_tentatives_on_reçoit_un_tableau_de_tentatives()
	{
		$avancement = new Avancement(Question::ETAT_DEBUT, Question::TYPE_PROG, [
			new TentativeProg("python", "codeTestPython", 1614711760, false, 2, "feedbackTest Python"),
			new TentativeProg("java", "codeTestJava", 1614711761, true, 2, "feedbackTest Java"),
		]);
		$avancement->id =
			"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24";

		$avancementTransformer = new AvancementTransformer();
		$résultats_obtenus = $avancementTransformer->includeTentatives($avancement);

		$tentatives = [];
		foreach ($résultats_obtenus->getData() as $résultat) {
			$tentatives[] = $résultat;
		}
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/avancementTransformerTest_2.json",
			json_encode($tentatives),
		);
	}
	public function test_étant_donné_un_avancement_avec_ses_sauvegardes_lorsquon_inclut_les_sauvegardes_on_reçoit_un_tableau_de_sauvegardes()
	{
		$sauvegardes = [];
		$sauvegardes["python"] = new Sauvegarde(1620150294, "print(\"Hello world!\")");
		$sauvegardes["java"] = new Sauvegarde(1620150375, "System.out.println(\"Hello world!\");");
		$avancement = new Avancement(Question::ETAT_DEBUT, Question::TYPE_PROG, [], $sauvegardes);
		$avancement->id =
			"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24";

		$avancementTransformer = new AvancementTransformer();
		$résultats_obtenus = $avancementTransformer->includeSauvegardes($avancement);

		$listeSauvegardes = [];
		foreach ($résultats_obtenus->getData() as $résultat) {
			$listeSauvegardes[] = $résultat;
		}
		$this->assertJsonStringEqualsJsonFile(
			__DIR__ . "/résultats_attendus/avancementTransformerTest_3.json",
			json_encode($listeSauvegardes),
		);
	}

	public function test_étant_donné_un_avancement_sans_tentative_lorsquon_inclut_les_tentatives_on_reçoit_un_tableau_vide()
	{
		$avancement = new Avancement("https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", "jdoe");
		$avancement->id =
			"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24";
		$avancement->tentatives = [];
		$avancement->type = Question::TYPE_PROG;

		$avancementTransformer = new AvancementTransformer();
		$this->assertEquals([], $avancementTransformer->includeTentatives($avancement)->getData());
	}
	public function test_étant_donné_un_avancement_sans_sauvegarde_lorsquon_inclut_les_sauvegardes_on_reçoit_un_tableau_vide()
	{
		$avancement = new Avancement("https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction", "jdoe");
		$avancement->id =
			"jdoe/aHR0cHM6Ly9kZXBvdC5jb20vcm9nZXIvcXVlc3Rpb25zX3Byb2cvZm9uY3Rpb25zMDEvYXBwZWxlcl91bmVfZm9uY3Rpb24";
		$avancement->sauvegardes = [];
		$avancement->type = Question::TYPE_PROG;

		$avancementTransformer = new AvancementTransformer();
		$this->assertEquals([], $avancementTransformer->includeSauvegardes($avancement)->getData());
	}
}
