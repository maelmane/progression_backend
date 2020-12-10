<?php

require_once __DIR__ . '/avancement.php';

class AvancementBD extends Avancement
{
    public $conteneur;
    public $reponse;
    public $code;

    public function __construct(
        $question_id,
        $user_id,
        $conteneur,
        $reponse = null,
        $code = null
    ) {
        parent::__construct($question_id, $user_id);
        $this->reponse = $reponse;
        $this->conteneur = $conteneur;
        $this->code = $code;
    }
}

?>
