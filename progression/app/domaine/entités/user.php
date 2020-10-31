<?php

require_once __DIR__ . "/entite.php";

class User extends Entite
{
    const ROLE_NORMAL = 0;
    const ROLE_ADMIN = 1;

    public $username;
    public $role = User::ROLE_NORMAL;
    public $id;
}

?>
