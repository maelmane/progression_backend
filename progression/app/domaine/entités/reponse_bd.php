<?php
class RéponseBD{

	public $conteneur;
    public $réponse;
    public $langid;
    public $code;

    public function __construct($langid, $code, $conteneur, $réponse){
        $this->langid=$langid;
        $this->code=$code;
        $this->conteneur=$conteneur;
        $this->réponse=$réponse;
    }

}
?>
