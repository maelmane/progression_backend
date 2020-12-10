<?php

require_once 'domaine/entités/question.php';
require_once __DIR__ . '/interacteur.php';

class SauvegarderAvancementSysInt extends Interacteur
{
    function __construct($source, $user_id)
    {
        parent::__construct($source);
        $this->_user_id = $user_id;
    }

    public function sauvegarder($avancement)
    {
        $dao = $this->_source->get_avancement_sys_dao();
        if ($avancement->etat == Question::ETAT_DEBUT) {
            $avancement->etat = QUESTION::ETAT_NONREUSSI;
        }
        $dao->save($avancement);
    }
}
