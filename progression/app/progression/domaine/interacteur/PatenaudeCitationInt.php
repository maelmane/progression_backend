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

class PatenaudeCitationInt extends Interacteur{
	const CapitainePatenaude = [
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
		"Si l’on ne trouve pas de solution, la mort pourrait nous tuer. (Capitaine Patenaude)",
		"Retroussons notre courage et prenons nos manches à deux mains. (Capitaine Patenaude)",
		"Jouer au ping-pong tout seul, c’est bon pour le cardio-vasculaire, mais c’est plate rare ! (Capitaine Patenaude)",
	];

	public function get_citation(){
		return self::CapitainePatenaude[rand(0, count(self::CapitainePatenaude) - 1)];
	}
}
