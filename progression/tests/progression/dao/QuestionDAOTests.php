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

use progression\domaine\entité\{QuestionProg, Exécutable, Test};
use PHPUnit\Framework\TestCase;
use Mockery;

final class QuestionDAOTests extends TestCase
{
	public function setUp(): void
	{
		$mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
		$mockDAOFactory
			->allows()
			->get_question_prog_dao()
			->andReturn(new QuestionProgDAO());

		DAOFactory::setInstance($mockDAOFactory);
	}

	public function tearDown(): void
	{
		parent::tearDown();

		Mockery::close();
	}

	public function test_get_question()
	{
		$question = new QuestionProg();
		$question->uri = "file://" . __DIR__ . "/démo/boucles/boucle_énumérée";
		$question->titre = "Affichage répété";
		$question->description = "Exercice simple sur les itérations à nombre d'itérations fixe";
		$question->enonce =
			"Saisissez un nombre sur l'entrée standard puis faites afficher la phrase «Bonjour le monde!» autant de fois.";
		$question->feedback_neg = "Pour tout savoir sur les itérations énumérées : [clique ici](http://unlien.com)";
		$question->feedback_pos = "Bravo! tu es prêt à passer à un type de boucles plus complexe";

		// Ébauches
		$question->exécutables = [];
		$question->exécutables["python"] = new Exécutable(
			"#+VISIBLE\nnb_répétitions = int( input() )\n\n#+TODO\n\nprint( \"Bonjour le monde\" )\n\n#-TODO\n\n#-VISIBLE\n",
			"python",
		);
		$question->exécutables["java"] = new Exécutable(
			"import java.util.Scanner;\n\npublic class exec {\n\n//+VISIBLE\n\npublic static void main(String[] args) {\n\n	Scanner input = new Scanner( System.in );\n		\n	nb_répétitions = input.nextInt();\n\n//+TODO\n\n	System.out.println( \"Bonjour le monde\" );\n\n//-TODO\n\n	}\n	\n//-VISIBLE\n\n}\n",
			"java",
		);

		// Tests
		$question->tests = [
			new Test(
				"10 fois",
				"10",
				"Bonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\nBonjour le monde\n",
			),
			new Test("1 fois", "1", "Bonjour le monde"),
			new Test(
				"0 fois",
				"0",
				"",
				"",
				"Bien joué! 0 est aussi une entrée valable.",
				"N'oublie pas les cas limites, 0 est aussi une entrée valable!",
			),
			new Test("2 fois", "2", "Bonjour\nBonjour\n", "", "Bien joué!", "Rien à dire"),
		];

		$résultat_obtenu = (new QuestionDAO())->get_question("file://" . __DIR__ . "/démo/boucles/boucle_énumérée");

		$this->assertEquals($question, $résultat_obtenu);
	}

	/** Impossible à tester tant qu'on n'aura pas séparé QuestionDAO et sa source de fichiers (Voir ticker #76)
	   public function test_étant_donné_un_zip_existant_contenant_une_question_lorsquon_donne_son_chemin_on_obtient_un_objet_question_prog_correspondant()
	   {
	   $question = new QuestionProg();
	   $question->uri = "file://" . sys_get_temp_dir() . __DIR__ . "/démo/appeler_une_fonction_paramétrée";
	   $question->titre = "Appeler une fonction paramétrée";
	   $question->description = "Appel d'une fonction existante recevant un paramètre";
	   $question->enonce =
	   "La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.";
	   $question->feedback_neg = "Avez-vous utilisé le parenthèse avec la variable nb_entré à l'intérieur?";
	   $question->feedback_pos = "Très bien! Vous avez maintenant appélé une fonction paramétrée";

	   // Ébauches
	   $question->exécutables = [];
	   $question->exécutables["python"] = new Exécutable(
	   "# +VISIBLE
	   def salutations( nb_répétitions ):
       for i in range( nb_répétitions ):
       print( \"Bonjour le monde!\" )


	   nb_entré = int( input() )
	   # +TODO


	   # -TODO
	   # -VISIBLE
	   ",
	   "python",
	   );
	   $question->exécutables["java"] = new Exécutable(
	   "import java.util.Scanner;

	   class Test {

	   // +VISIBLE

	   public static void salutations( int nb_répétitions ) {
	   for ( int i = 0; i < nb_répétitions; i++ ) {
	   System.out.println( \"Bonjour le monde!\" );
	   }
	   }

	   public static void main( String[] args ) {
	   Scanner scan = new Scanner( System.in );

	   int nb_entré = scan.nextInt();

	   // +TODO



	   // -TODO
	   }
	   // -VISIBLE
	   }
	   ",
	   "java",
	   );

	   // Tests
	   $question->tests = [
	   new Test("Une salutation", 1, "Bonjour le monde!\n", "", "Bravo champion!", "Encore un effort..."),
	   new Test(
	   "10 salutations",
	   10,
	   "Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   ",
	   ),
	   new Test(
	   "100 salutations",
	   100,
	   "Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   Bonjour le monde!
	   ",
	   ),
	   new Test(
	   "Aucune salutation",
	   0,
	   "",
	   "",
	   "Bien vu! 0 salutations est une valeur possible.",
	   "Que veut-on voir lorsqu'on demande 0 salutations?",
	   ),
	   ];

	   $mockDAOFactory = Mockery::mock("progression\dao\DAOFactory");
	   $mockDAOFactory
	   ->allows()
	   ->get_question_prog_dao()
	   ->andReturn(new QuestionProgDAO());

	   $résultat_obtenu = (new QuestionDAO($mockDAOFactory))->get_question(
	   "file://" . __DIR__ . "/démo/appeler_une_fonction_paramétrée.zip",
	   );
	   $this->assertEquals($question, $résultat_obtenu);
	   }
	 */

	public function test_étant_donnée_un_fichier_info_vide_lorsquon_récupère_la_question_on_obtien_une_QuestionProg_avec_des_attributs_par_défaut()
	{
		$résultat_attendu = new QuestionProg();
		$résultat_attendu->uri = "file://" . __DIR__ . "/démo/défauts";

		$résultat_obtenu = (new QuestionDAO())->get_question("file://" . __DIR__ . "/démo/défauts");

		$this->assertEquals($résultat_attendu, $résultat_obtenu);
	}
}
