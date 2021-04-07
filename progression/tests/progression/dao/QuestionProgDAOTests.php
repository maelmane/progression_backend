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

use progression\domaine\entité\{Exécutable, QuestionProg, Test};
use PHPUnit\Framework\TestCase;

final class QuestionProgDAOTests extends TestCase
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

		$info["execs"] = [
			"python" => '#+VISIBLE
nb_répétitions = int( input() )

#+TODO

print( "Bonjour le monde" )

#-TODO

#-VISIBLE
',
			"java" => 'import java.util.Scanner;

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
		];
		$info["tests"] = [

			[
				"nom" => "10 fois",
				"in" => 10,
				"out" => "Bonjour le monde
Bonjour le monde
Bonjour le monde
Bonjour le monde
Bonjour le monde
Bonjour le monde
Bonjour le monde
Bonjour le monde
Bonjour le monde
Bonjour le monde
",
			],

			[
				"nom" => "1 fois",
				"in" => 1,
				"out" => "Bonjour le monde",
			],

			[
				"nom" => "0 fois",
				"in" => 0,
				"out" => "",
				"params" => null,
				"feedback+" => "Bien joué! 0 est aussi une entrée valable.",
				"feedback-" => "N'oublie pas les cas limites, 0 est aussi une entrée valable!",
			],

			[
				"nom" => "2 fois",
				"in" => 2,
				"out" => "Bonjour
Bonjour
",
				"params" => null,
				"feedback+" => "Bien joué!",
				"feedback-" => "Rien à dire",
			],
		];

		$résultat_observé = new QuestionProg();
		(new QuestionProgDAO())->load($résultat_observé, $info);
		$this->assertEquals($résultat_attendu, $résultat_observé);
	}
}
