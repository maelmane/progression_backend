<?php

require_once('entite.php');
require_once('serie.php');

class Theme extends Entite{

    public $titre;
    public $description;

    function get_pourcentage_avancement($user_id){
		return floor($this->get_avancement($user_id)/$this->get_nb_questions_actives()*100);
    }
}
?>
