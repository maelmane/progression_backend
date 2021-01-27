<?php

require_once 'dao/theme.php';

class MockThèmeDAO extends ThèmeDAO{


    static function get_thèmes($inactif = false){
        return [MockThèmeDAO::get_thème(0), MockThèmeDAO::get_thème(1), MockThèmeDAO::get_thème(2)];
    }

    static function get_thème($id)
    {
        $thème = new Thème($id);

        MockThèmeDAO::load($thème);

        return $thème;
    }

    protected static function load($objet){
        $objet->titre = "Thème factice #" . $objet->id;
        $objet->description = "Ceci est le thème factice numéro " . $objet->id;
        $objet->séries_id = [ 8, 22, 43 ];
    }
    
    static function get_nb_questions_actives($id){
        return 42;
    }

    function get_avancement($thème_id, $user_id){
        return 8;
    }

}
