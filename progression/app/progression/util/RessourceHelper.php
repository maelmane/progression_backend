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

class RessourceHelper
{
	public array $urlArray;
	public array $methodArray;

    
	private function constructeur0() {
        $this->urlArray = ["*"];
        $this->methodArray = ["*"];
		$this->ressources = [$this->urlArray, $this->methodArray];
    }
	
	private function constructeur1($json) {
		$jsonDécodé = json_decode($json, false);
        $this->urlArray = $jsonDécodé->urlArray;
        $this->methodArray = $jsonDécodé->methodArray;
		$this->ressources = [$this->urlArray, $this->methodArray];
    }

	private function constructeur2($urlArray, $methodArray) {
        $this->urlArray = $urlArray;
        $this->methodArray = $methodArray;
		$this->ressources = [$this->urlArray, $this->methodArray];
    }

	public function __construct() {
        $paramètres = func_get_args();
        $nombreDeParamètres = func_num_args();
  
        if (method_exists($this, $function = 
                "constructeur".$nombreDeParamètres)) {
            call_user_func_array(
                        array($this, $function), $paramètres);
        }
    }

    public function obtenirEnJson() {
        return json_encode(["ressources" => ["url" => $this->urlArray, "method" => $this->methodArray]]);
    }

	public function vérifierSiContientUrl($url) {
		return $this->vérifierÉgalitéSelonWildcard($url, $this->urlArray);
	}

	public function vérifierSiContientMethod($method) {
		return $this->vérifierÉgalitéSelonWildcard($method, $this->methodArray);
	}

	private function vérifierÉgalitéSelonWildcard($élément, $élémentArray) {
		$positionWildcard = strpos($élément, "*");
		$estÉgal = false;
		
		if ($positionWildcard === 0) {
			$estÉgal = true;
		} elseif ($positionWildcard === false) {
			$estÉgal = in_array($élément, $élémentArray);
		} else {
			$élémentTronqué = substr($élément, 0, $positionWildcard - 1);
			foreach ($élémentArray as $e) {
				if (substr($e, 0, $positionWildcard - 1) == $élémentTronqué) {
					$estÉgal = true;
				}
			}
		}
		
		return $estÉgal;
	}
}