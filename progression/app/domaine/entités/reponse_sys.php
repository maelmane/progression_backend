<?php

class RéponseSys{

	public $conteneur;
    public $réponse;

    public function __construct($conteneur, $réponse){
        $this->conteneur=$conteneur;
        $this->réponse=$réponse;
    }

}
?>
