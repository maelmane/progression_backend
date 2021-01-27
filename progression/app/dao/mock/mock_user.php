<?php

require_once 'dao/user.php';

class MockUserDAO extends UserDAO{
    public function trouver_par_nomusager($username){
        if ($username == "admin"){
            return MockUserDAO::get_user(0);
        }
        else{
            return MockUserDAO::get_user(42);
        }
    }

    public function get_user($user_id)
    {
        $user = new User($user_id);
        UserDAO::load($user);

        return $user;
    }

    protected function load($objet){
        if ($objet->id == 0){
            $objet->username = "admin";
            $objet->role = User::ROLE_ADMIN;
        }
        if ($objet->id == 42){
            $objet->username = "bob";
            $objet->role = User::ROLE_NORMAL;
        }
    }
}

?>
