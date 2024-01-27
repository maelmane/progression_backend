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

namespace progression\http\contrôleur;

class GénérateurAléatoire
{
	private static ?GénérateurAléatoire $instance = null;

	private function __construct()
	{
	}

	static function get_instance(): GénérateurAléatoire
	{
		if (GénérateurAléatoire::$instance == null) {
			GénérateurAléatoire::$instance = new GénérateurAléatoire();
		}

		return GénérateurAléatoire::$instance;
	}

	static function set_instance(?GénérateurAléatoire $générateur): void
	{
		GénérateurAléatoire::$instance = $générateur;
	}

	function générer_chaîne_aléatoire(
		int $taille = 64,
		string $alphabet = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ",
	): string {
		if ($taille < 0) {
			throw new \RangeException("La taille ne peut pas être négative");
		}
		$pieces = [];
		$max = mb_strlen($alphabet, "8bit") - 1;
		if ($max < 1) {
			throw new \InvalidArgumentException("L'alphabet ne peut être vide");
		}
		for ($i = 0; $i < $taille; ++$i) {
			$pieces[] = $alphabet[random_int(0, $max)];
		}
		return implode("", $pieces);
	}
}
