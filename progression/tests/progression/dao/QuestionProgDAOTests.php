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

require_once __DIR__ . "/../../TestCase.php";

use progression\domaine\entité\{Avancement, Exécutable, QuestionProg, Question, Test};
use progression\dao\AvancementDAO;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

final class QuestionProgDAOTests extends \TestCase
{
	public function setUp(): void
	{
		EntitéDAO::get_connexion()->begin_transaction();
	}

	public function tearDown(): void
	{
		EntitéDAO::get_connexion()->rollback();
	}

	public function test_étant_donné_une_question_prog_existante_lorsquon_donne_une_question_et_les_infos_on_obtient_un_objet_question_prog_correspondant()
	{
		$résultat_attendu = new QuestionProg();
		$résultat_attendu->type = Question::TYPE_PROG;
		$résultat_attendu->uri = "file://" . __DIR__ . "/démo/boucles/boucle_énumérée";
		$résultat_attendu->titre = "Affichage répété";
		$résultat_attendu->description = "Exercice simple sur les itérations à nombre d'itérations fixe";
		$résultat_attendu->enonce = "Saisissez un nombre sur l'entrée standard puis faites afficher la phrase «Bonjour le monde!» autant de fois.";
		$résultat_attendu->feedback_pos = "Bravo! tu es prêt à passer à un type de boucles plus complexe";
		$résultat_attendu->feedback_neg = "Pour tout savoir sur les itérations énumérées : [clique ici](http://unlien.com)";
		$résultat_attendu->exécutables = [
			"python" => new Exécutable(
				'#+VISIBLE
nb_répétitions = int( input() )

#+TODO

print( "Bonjour le monde" )

#-TODO

#-VISIBLE
',
				"python"
			),
			"java" => new Exécutable(
				'import java.util.Scanner;

public class exec {

//+VISIBLE

public static void main(String[] args) {

	Scanner input = new Scanner( System.in );
		
	nb_répétitions = input.nextInt();

//+TODO

	System.out.println( "Bonjour le monde" );

//-TODO

	}
	
//-VISIBLE

}
',
				"java"
			),
		];
		$résultat_attendu->tests = [
			new Test(
				"10 fois",
				10,
				"Bonjour le monde
Bonjour le monde
Bonjour le monde
Bonjour le monde
Bonjour le monde
Bonjour le monde
Bonjour le monde
Bonjour le monde
Bonjour le monde
Bonjour le monde
"
			),
			new Test(
				"1 fois",
				1,
				"Bonjour le monde"
			),
			new Test(
				"0 fois",
				0,
				"",
				null,
				"Bien joué! 0 est aussi une entrée valable.",
				"N'oublie pas les cas limites, 0 est aussi une entrée valable!"
			),
			new Test(
				"2 fois",
				2,
				"Bonjour
Bonjour
",
				null,
				"Bien joué!",
				"Rien à dire"
			),
		];

		$résultat_observé = new QuestionProg();
		// Analyse de fichier
		$data = file_get_contents("file://" . __DIR__ . "/démo/boucles/boucle_énumérée/info.yml");
		if ($data === false) {
			error_log("file://" . __DIR__ . "/démo/boucles/boucle_énumérée ne peut pas être chargé");
			return null;
		}
		$info = yaml_parse($data);
		if ($info == false) {
			error_log("file://" . __DIR__ . "/démo/boucles/boucle_énumérée ne peut pas être décodé");
			return null;
		}
		if (isset($info["type"]) && $info["type"] == "prog") {
			$info = (new QuestionProgDAO())->récupérer_question("file://" . __DIR__ . "/démo/boucles/boucle_énumérée", $info);
		}
		$info["uri"] = "file://" . __DIR__ . "/démo/boucles/boucle_énumérée";
		$résultat_observé->uri = $info["uri"];
		$résultat_observé->titre = $info["titre"];
		$résultat_observé->description = $info["description"];
		$résultat_observé->enonce = $info["énoncé"];
		$résultat_observé->feedback_pos = key_exists("feedback+", $info) ? $info["feedback+"] : null;
		$résultat_observé->feedback_neg = key_exists("feedback-", $info) ? $info["feedback-"] : null;


		(new QuestionProgDAO())->load($résultat_observé, $info);
		$this->assertEquals($résultat_attendu, $résultat_observé);
	}
}
