<?php

require_once "domaine/entités/user.php";

class UserDAO extends EntiteDAO
{
    public function existe($username)
    {
        return !is_null($this->trouver_par_nomusager($username));
    }

    public function trouver_par_nomusager($username)
    {
        $id = null;

        $query = $GLOBALS["conn"]->prepare(
            'SELECT userID FROM users WHERE username = ?'
        );
        $query->bind_param("s", $username);
        $query->execute();
        $query->bind_result($id);
        $query->fetch();
        $query->close();

        if ($id == null) {
            return null;
        } else {
            return UserDAO::get_user($id);
        }
    }

    public function get_user($user_id)
    {
        $user = new User($user_id);
        UserDAO::load($user);

        return $user;
    }

    protected function load($objet)
    {
        $query = $GLOBALS["conn"]->prepare(
            'SELECT userID, username, role FROM users WHERE userID = ? '
        );
        $query->bind_param("i", $objet->id);
        $query->execute();

        $query->bind_result($objet->id, $objet->username, $objet->role);
        $res = $query->fetch();
        $query->close();
    }

    public function save($objet)
    {
        $query = $GLOBALS["conn"]->prepare(
            'INSERT INTO users( username, role ) VALUES ( ?, ? ) ON DUPLICATE KEY UPDATE role=VALUES( role )'
        );
        $query->bind_param("si", $objet->username, $objet->role);
        $query->execute();
        $query->close();

        return $this->trouver_par_nomusager($objet->username);
    }
}
?>
