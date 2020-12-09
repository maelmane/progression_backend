<?php

require_once __DIR__ . '/avancement.php';

class AvancementSys extends Avancement
{
    public $conteneur;
    public $reponse;

    public function __construct(
        $question_id,
        $user_id,
        $conteneur = null,
        $reponse = null
    ) {
        parent::__construct($question_id, $user_id);
        $this->reponse = $reponse;
        $this->conteneur = $conteneur;
    }
}

?>
