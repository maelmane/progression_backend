<?php

require_once('entite.php');
require_once('avancement.php');

class Question extends Entite{
    //Constantes d'état
    const ETAT_CACHE=-1;
    const ETAT_DEBUT=0;
    const ETAT_NONREUSSI=1;
    const ETAT_REUSSI=2;

    //Constantes de type
    const TYPE_PROG=0;
    const TYPE_SYS=1;
    
    //Données
    public $serieID;
    public $actif;
    public $numero;
    public $titre;
    public $description;
    public $enonce;
    public $etat;
    public $code_validation;
    public $avancement;
    
    public function get_avancement($user_id){
        if (is_null($this->avancement))
            $this->avancement=new Avancement($this->id, $user_id);
        return $this->avancement;
    }
    
}

?>
