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

use progression\domaine\entité\{QuestionProg, TentativeProg, Test, RésultatProg};
use PHPUnit\Framework\TestCase;

final class TraiterTentativeProgIntTests extends TestCase
{
	public function test_étant_donné_une_TentativeProg_correcte_lorsquon_les_traite_on_obtient_une_TentativeProg_traitée_et_réussie()
	{
		$question = new QuestionProg();
		$question->tests = [
			new Test("premier test", "1", "ok\n", null, "Test 0 passé", "Test 0 échoué"),
			new Test("deuxième test", "5", "ok\nok\nok\nok\nok\n", null, "Test 1 passé", "Test 1 échoué"),
		];
		$question->feedback_pos = "Bravo!";
		$question->feedback_neg = "Non!";

		$tentative = new TentativeProg("python", "testCode");
		$tentative->résultats = [new RésultatProg("ok\n", ""), new RésultatProg("ok\nok\nok\nok\nok\n", "")];
		$résultat_attendu = new TentativeProg("python", "testCode", null, true, 2, "Bravo!", [
			new RésultatProg("ok\n", "", true, "Test 0 passé"),
			new RésultatProg("ok\nok\nok\nok\nok\n", "", true, "Test 1 passé"),
		]);

		$résultat_observé = (new TraiterTentativeProgInt(null))->traiter_résultats($question, $tentative);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_TentativeProg_incorrecte_lorsquon_les_traite_on_obtient_une_TentativeProg_traitée_et_nonréussie_avec_feedback_positif()
	{
		$question = new QuestionProg();
		$question->tests = [
			new Test("premier test", "1", "ok\n", null, "Test 0 passé", "Test 0 échoué"),
			new Test("deuxième test", "5", "ok\nok\nok\nok\nok\n", null, "Test 1 passé", "Test 1 échoué"),
			new Test(
				"troisième test",
				"10",
				"ok\nok\nok\nok\nok\nok\nok\nok\nok\nok\n",
				null,
				"Test 2 passé",
				"Test 2 échoué",
			),
		];
		$question->feedback_pos = "Bravo!";
		$question->feedback_neg = "As-tu essayé de ne pas faire ça?";

		$tentative = new TentativeProg("python", "testCode");
		$tentative->résultats = [
			new RésultatProg("ok\n", ""),
			new RésultatProg("ok\nok\nok\n", ""),
			new RésultatProg("ok\nok\nok\nok\nok\n", ""),
		];

		$résultat_attendu = new TentativeProg(
			"python",
			"testCode",
			null,
			false,
			1,
			"As-tu essayé de ne pas faire ça?",
			[
				new RésultatProg("ok\n", "", true, "Test 0 passé"),
				new RésultatProg("ok\nok\nok\n", "", false, "Test 1 échoué"),
				new RésultatProg("ok\nok\nok\nok\nok\n", "", false, "Test 2 échoué"),
			],
		);

		$résultat_observé = (new TraiterTentativeProgInt(null))->traiter_résultats($question, $tentative);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_TentativeProg_avec_une_erreur_et_un_feedback_d_erreur_prévu_traites_on_obtient_une_TentativeProg_traitée_et_nonréussie_avec_feedback_d_erreur()
	{
		$question = new QuestionProg();
		$question->tests = [
			new Test("premier test", "1", "ok\n", null, "Test 0 passé", "Test 0 échoué", "Erreur!"),
			new Test("deuxième test", "5", "ok\nok\nok\nok\nok\n", null, "Test 1 passé", "Test 1 échoué", "Erreur!"),
		];
		$question->feedback_pos = "Bravo!";
		$question->feedback_neg = "As-tu essayé de ne pas faire ça?";
		$question->feedback_err = "Revise la syntaxe de ton code";

		$tentative = new TentativeProg("python", "testCode");
		$tentative->résultats = [new RésultatProg("ok\n", ""), new RésultatProg("", "testErreur")];

		$résultat_attendu = new TentativeProg(
			"python",
			"testCode",
			null,
			false,
			1,
			"Revise la syntaxe de ton code",
			[new RésultatProg("ok\n", "", true, "Test 0 passé"), new RésultatProg("", "testErreur", false, "Erreur!")],
		);

		$résultat_observé = (new TraiterTentativeProgInt(null))->traiter_résultats($question, $tentative);

		$this->assertEquals($résultat_attendu, $résultat_observé);
	}

	public function test_étant_donné_une_TentativeProg_avec_une_erreur_sans_feedback_d_erreur_prévu_traites_on_obtient_une_TentativeProg_traitée_et_nonréussie_avec_feedback_négatif()
	{
		$citations = [
			"Les logiciels sont comme le sexe, c'est meilleur lorsque c'est libre. (Linus Torvalds)",
			"Vraiment, je ne suis pas là pour détruire Microsoft. Ca sera juste un effet secondaire tout à fait involontaire. (Linus Torvalds)",
			"Si la connerie était sans fil, ça ferait bien longtemps que mon voisin serait à la pointe de la technologie. (N.Annereau)",
			"Hacker vaillant, rien d'impossible.",
			"Il y a 10 types de personnes dans le monde : ceux qui comprennent le binaire et ceux qui ne le comprennent pas.",
			"Il y a 11 types de personnes dans le monde : ceux qui comprennent le binaire réfléchi et ceux qui ne le comprennent pas.",
			"Le vrai danger, ce n'est pas quand les ordinateurs penseront comme les hommes, c'est quand les hommes penseront comme les ordinateurs. (Sydney Harris)",
			"Le principe de l'évolution est beaucoup plus rapide en informatique que chez le bipède. (Jean Dion)",
			"L'avènement du cyberespace a eu pour principale conséquence d'abaisser le seuil de patience de l'humain postmoderne à un dixième de seconde. (Jean Dion)",
			"L'ordinateur est un appareil sophistiqué auquel on fait porter une housse la nuit en cas de poussière et le chapeau durant la journée en cas d'erreur. (Philippe Bouvard)",
			"Les trous noirs, ce sont les endroits où Dieu a fait des divisions par zéro.",
			"Commit du soir, espoir. Build du matin, chagrin.",
			"L'époque des PC est terminée. (Lou Gerstner, Directeur d'IBM, 1998)",
			"Je crois qu'OS/2 est destiné à être le système d'exploitation le plus important de tous les temps. (Bill Gates, PDG et fondateur de Microsoft, 1988)",
			"La souris, qu'est-ce que c'est ? (Jacques Chirac devant un ordinateur, décembre 1996)",
			"Les virus ne seraient souvent, au départ, que de vulgaires erreurs de programmation, des bugs. (Le Nouvel Observateur, cité dans SVM, juin 1994)",
			"Selon le cabinet d'études Strategic Inc., Ethernet est condamné par son manque d'avantages économiques et techniques. (LMI, 1981)",
			"Il n'y a aucune raison pour laquelle quiconque désirerait avoir un ordinateur à la maison. (Ken Olsen, président fondateur de Digital, 1977)",
			"Si vous désirez voyager autour du monde et être invité a discourir un peu partout il suffit d'écrire une version d'Unix. (Linus Torvalds)",
			"L'informatique, en tant que discipline, ne traite pas plus des ordinateurs que l'astronomie ne le fait des téléscopes. (E. W. Dijkstra)",
			"Les simulations, comme les bikinis, montrent pas mal de choses mais cachent le principal. (Hubert Kirrman)",
			"Prétendre que le secret des sources est une sécurité supplémentaire est un abus de confiance caractérisé. (PC Expert octobre 98)",
			"Demander si un ordinateur peut penser revient à demander si un sous-marin peut nager. (Edsgar Dijkstra)",
			"Une organisation traitant ses développeurs comme s'ils étaient abrutis se retrouvera vite riche d'une équipe d'informaticiens uniquement capables de se comporter comme s'ils l'étaient. (B. Stroustrup)",
			"Le nombre de prédictions concernant la fin de la loi de Moore tend à doubler tous les 18 mois. (H. Eychenne)",
			"Les ordinateurs sont inutiles. Ils ne donnent que des réponses. (Pablo Picasso)",
			"Je pense qu'il y a un marché mondial pour environ 5 ordinateurs. (Thomas WATSON, président d'IBM, 1943)",
			"Je n'ai pas peur des ordinateurs. J'ai peur qu'ils viennent à nous manquer. (Isaac Asimov)",
			"Sur internet, on peut écouter la radio tout en payant le téléphone. (Anne Roumanoff)",
			"Internet, c'est dingue : on y cherche rien et on trouve tout ! (Anne Roumanoff)",
			"Si nous avions su dès le départ qu'il fallait gagner de l'argent, nous l'aurions fait. (Antoine Bourdillon, DG de la startup Clicvision, mars 2001)",
			"Qui a besoin de voir des films d'horreur lorsqu'il a déjà Windows 95 ? (Christine Comaford, PC Week, 27 septembre 1995)",
			"S'il faut deux secondes pour transmettre une page par le réseau Numeris et quarante-cinq secondes pour l'imprimer, il y a un problème. (F. Hodbert, directeur des produits Numeris chez Matra)",
			"La vitesse a toujours été importante, sinon nul n'aurait besoin d'ordinateur. (S. Cray)",
			"Lorsque vous ne savez pas utiliser l'outil informatique, vous ne vous rendez pas compte de la chance que vous avez tant que vous n'apprenez pas à vous en servir.(Mereck)",
			"Le libre, c'est avant tout d'avoir de la notoriété. (Wikipedia FR)",
			"Méfiez vous d'un ordinateur que vous ne pouvez jeter par la fenêtre. (Steve Wozniak)",
			"Avec Windows 98, nous étions au bord du gouffre. Avec Windows ME, nous avons fait un grand pas en avant.",
			"Qui pourrait se sentir mal de pirater Windows XP? Ils nous ont pourtant infligé ME et 95!",
			"L'informatique c'est passer 15 jours a gagner 15 mili-secondes."
		];

		$CapitainePatenaude = [
			"Qui sème le vent récolte du blé d’inde pis des pétates. (Capitaine Patenaude)",
			"Il est parti comme une vache dans un jeu de quilles ! (Capitaine Patenaude)",
			"Comme disait le grand Jules César : Oh boy qu’on a passé proche ! (Capitaine Patenaude)",
			"La nuit porte… porte… porte de garage. (Capitaine Patenaude)",
			"Rien ne sert de courir: il faut partir à go, encaisser 200$ et acheter l’avenue Connecticut. (Capitaine Patenaude)",
			"Faut Voyager loin en autobus … Pour se rendre sur la Lune ! (Capitaine Patenaude)",
			"Comme disait le grand Pierre de Coubertin, la volonté c’est comme l’acné ! Plus t’en a, et plus…. ça paraît ! (Capitaine Patenaude)",
			"On ne doit pas vendre la peau de l’ours avant de l’avoir mise devant les bœufs. (Capitaine Patenaude)",
			"Comme disait le grand Jules César: c’est l’fun ya pas d’bebittes. (Capitaine Patenaude)",
			"Il ne faut tout de même pas vendre la peau de l’ours s’il est pas d’accord avec le prix ! (Capitaine Patenaude)",
			"Vini vidi vitchi ce qui veux dire: je vais y aller, je vais checker, pis m’en va vous rapp’ller. (Capitaine Patenaude)",
			"C’est le grand Jules César qui disait devant les grands Ottomans: let’s go les gars, on a pas d’temps à perdre. (Capitaine Patenaude)",
			"Folaisséembrazé c’est un mot Allemand. Ca veut dire: awaille, déguedine, t’es capable. (Capitaine Patenaude)",
			"Après la pluie, le gazon est mouillé. (Capitaine Patenaude)",
			"Le vaisseau est entrain de fondre, comme un banc d’neige en Floride. (Capitaine Patenaude)",
			"Jules César a envahi la Gaule en disant: “On vient juste ramasser quelques framboises…” (Capitaine Patenaude)",
			"Toute vérité n’est pas toujours vraie… (Capitaine Patenaude)",
			"Il y a un seul terme scientifique pour ça et c’est: “Dring dring pow pow chicke chicke wow wow!” (Capitaine Patenaude)",
			"Comme disait le grand Sergaï: “C’est qui le twit qui a parké son char dans mon stationnement?” (Capitaine Patenaude)",
			"N’oubliez pas que c’est en forgeant qu’on devient Michel Forget. (Capitaine Patenaude)",
			"Qui vivra…ne mourra pas toute suite. (Capitaine Patenaude)",
			"Rien ne sert de courir LLLL….L’autobus est déjà passer. (Capitaine Patenaude)",
			"Rome ne s’est pas construit qu’en…qu’en criant lapin je ne boirais pas de ton eau. (Capitaine Patenaude)",
			"Quand on est seul on est souvent moins nombreux. (Capitaine Patenaude)",
			"Revenons à nos moutons parce que…parce que le berger nous les a confiés. (Capitaine Patenaude)",
			"Quand j’aime une fois, j’aime une fois. Et c’est ça qui est ça… (Capitaine Patenaude)",
			"L’avenir appartient à ceux qui se lave tôt, car … il reste encore de l’eau chaude. (Capitaine Patenaude)",
			"Comme disait le grand Jules césar: Maudit que la pêche à crotte est bonne c’t’année. (Capitaine Patenaude)",
			"Comme dirait Buffalo bill: Un bon alien, est alien mort. (Capitaine Patenaude)",
			"On fait pas d’omelette sans casser les oeufs. (Capitaine Patenaude)",
			"Si l’on ne trouve pas de solution, la mort pourrai nous tuer. (Capitaine Patenaude)",
			"Retroussons notre courage et prenons nos manches à deux mains. (Capitaine Patenaude)",
			"Jouer au ping-pong tout seul, c’est bon pour le cardio-vasculaire, mais c’est plate rare ! (Capitaine Patenaude)"
		];
		$question = new QuestionProg();
		$question->tests = [
			new Test("premier test", "1", "ok\n", null, "Test 0 passé", "Test 0 échoué"),
			new Test("deuxième test", "5", "ok\nok\nok\nok\nok\n", null, "Test 1 passé", "Test 1 échoué"),
		];
		$question->feedback_pos = "Bravo!";
		$question->feedback_neg = "As-tu essayé de ne pas faire ça?";

		$tentative = new TentativeProg("python", "testCode");
		$tentative->résultats = [new RésultatProg("ok\n", ""), new RésultatProg("", "testErreur")];

		$résultat_observé = (new TraiterTentativeProgInt(null))->traiter_résultats($question, $tentative);

		$date = date("j n");
		if ($date === "1 4") {
			$this->assertContains($résultat_observé->feedback, $CapitainePatenaude);
		} else {
			$this->assertContains($résultat_observé->feedback, $citations);
		}
	}
}
