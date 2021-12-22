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

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ExécuteurCache extends Exécuteur
{
	private $_exécuteur;
	
	public function __construct($exécuteur){
		$this->_exécuteur = $exécuteur;
	}
	
	public function exécuter($exécutable, $test)
	{
		$hash = $this->calculer_hash( $exécutable->code, $exécutable->lang, $test->entrée );
		
		$résultat = $this->obtenir_de_la_cache( $hash );

		if ($résultat) {
			return $résultat;
		}

		$hash_non_formaté = $hash;
		
		$code = $this->standardiser_code( $exécutable->code, $exécutable->lang );
		$hash = $this->calculer_hash( $exécutable->code, $exécutable->lang, $test->entrée );

		$résultat = $this->obtenir_de_la_cache( $hash );

		if(!$résultat) {
			$résultat = $this->_exécuteur->exécuter($exécutable, $test);

			$this->placer_en_cache( $hash, $résultat );
		}
		
		if (!$this->contient_des_erreurs( $résultat ))
		{
			$this->placer_en_cache( $hash_non_formaté, $résultat );
		}

		return $résultat;
	}

	private function calculer_hash( $code, $lang, $entrée ){
		return md5( $code . $lang . $entrée );
	}

	private function standardiser_code( $code, $langage ) {
		return $code;
	}
	
	private function obtenir_de_la_cache( $hash ){
		return Cache::get( $hash );
	}

	private function placer_en_cache( $hash, $résultat ){
		return Cache::put( $hash, $résultat );
	}

	private function contient_des_erreurs( $résultat ){
		return json_decode($résultat, true)["errors"];
	}
}
