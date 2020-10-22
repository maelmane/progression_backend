<?php 

require_once __DIR__.'/connecter_conteneur.php';

class RéinitialiserConteneurInt extends ConnecterConteneurInt {
	
	protected function get_data_rc(){
		return $this->get_data_nouveau_conteneur();
	}
}
?>
