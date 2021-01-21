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
?><?php
require_once 'controleur_admin.php';

class ControleurSuivi extends ControleurAdmin {

	function __construct( $id, $user_id ){
		parent::__construct( $id, $user_id );
	}

	function get_page_infos(){
		$infos=array( "template"=>"ad_suivi" );
		
		$infos=array_merge( $infos, $this->récupérer_paramètres() );

		return $infos;
	}

	function récupérer_paramètres(){
		$infos=array();
		
		if( !isset( $_GET[ 'u' ] )){
			$infos[ "users" ]=get_users();
		}
		elseif( !isset( $_GET[ 't' ] )){
			$infos[ "user_id" ]=$_GET[ 'u' ];
			$infos[ "thèmes" ]=get_themes();
			foreach( $infos[ "thèmes" ] as $thème ){
				$thème->avancement = $thème->get_pourcentage_avancement( $_GET[ "u" ] );
			}
		}
		else{
			$thème=new Theme( $_GET[ 't' ] );
			$infos[ "thème" ]=$thème;
			$infos[ "séries" ]=$thème->get_series();
			foreach( $infos[ "séries" ] as $série ){
				$série->avancement = $série->get_pourcentage_avancement( $_GET[ "u" ] );
			}
		}

		return $infos;
	}

}
?>
