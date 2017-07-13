<?php
session_start();

if(isset($_SESSION["login_user"])){
    header('Location: index.php');
}
else{
    if(isset($_POST["submit"])){
        if(empty($_POST["username"]) || empty($_POST["passwd"])){
            $erreur="Nom d'utilisateur ou mot de passe invalide.";
        }
        else{
            $username=$_POST[username];
            $password=$_POST[passwd];
            
            $ldap = ldap_connect("localhost") or die("Could not connect to LDAP server.");
            ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);   
            $bind = @ldap_bind($ldap, "cn=$username,dc=local", "$password");
            if($bind) {
                //Génère un ID
                $_SESSION["login_user"]=$username;
                header('Location: index.php');
            } else {
                $erreur="Nom d'utilisateur ou mot de passe invalide.";
            }
        }
    }

    echo '
         <html>
         <body>';

    if($erreur){
        echo $erreur;
    }
    echo '
         <form name="login" method="POST">
             <table>
             <tr>
             <td>Nom d\'utilisateur</td><td><input name="username" type="text"><td>
             </tr><tr>
             <td>Mot de passe</td><td><input name="passwd" type="password"><td>
             </tr><tr>
             <td></td><td><input name="submit" type="submit" value="Connexion"></td>
             </tr>
         </form>
         ';

}
