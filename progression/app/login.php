<?php

session_start();
require __DIR__ . '/../vendor/autoload.php';
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/modele.php');

load_config();

if(isset($_SESSION["user_id"])){
    header('Location: /index.php?p=accueil');
} else {
    if(!isset($_POST["submit"]) || !effectuer_login()){
        $infos=récupérer_infos();
        render_page($infos);
    }        
}

function effectuer_login(){
    $erreur="";

    $login=false;
    if($GLOBALS['config']['auth_type']=="no"){
        $login=login_sans_authentification();
    }
    elseif($GLOBALS['config']['auth_type']=="local"){
        $local=login_local();
    }
    elseif($GLOBALS['config']['auth_type']=="ldap"){
        $local=login_ldap();
    }

    return $login;
}

function login_local(){
        $erreure="L'authentification locale n'est pas implémentée.";
        return false;
}

function login_sans_authentification(){
        return vérifier_champs_valides();
}

function login_ldap(){
        vérifier_champs_valides();
        $user=get_utilisateur_ldap();
        if($user['count']==0){
            $erreur="Nom d'utilisateur ou mot de passe invalide.";
            return false;
        }
        if(!vérification_mdp_ldap()){
            $erreur="Nom d'utilisateur ou mot de passe invalide.";
            return false;
        }
        $user=get_user($username);
        get_infos_session($user);

        return true;
}

function vérifier_champs_valides(){
    if(empty($_POST["username"]) || empty($_POST["passwd"])){
        $erreur="Le nom d'utilisateur ou le mot de passe ne peuvent être vides.";
    }
}

function get_utilisateur_ldap(){

        $username=$_POST["username"];
        $password=$_POST["passwd"];

        #Tentative de connexion à AD
        define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);
            
        $ldap = ldap_connect("ldaps://".$GLOBALS['config']['hote_ad'],$GLOBALS['config']['port_ad']) or die("Impossible de se connecter au serveur d'authentification. Veuillez communiquer avec l'administrateur du site.");
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        $bind = @ldap_bind($ldap, $GLOBALS['config']['dn_bind'], $GLOBALS['config']['pw_bind']);

        if(!$bind) {
            ldap_get_option($ldap, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error);
            $erreur="Impossible de se connecter au serveur d'authentification. Veuillez communiquer avec l'administrateur du site. Erreur : $extended_error";
        }
        $result=ldap_search($ldap, $GLOBALS['config']['domaine_ldap'], "(sAMAccountName=$username)", array('dn','cn',1));
        $user=ldap_get_entries($ldap, $result);

        return $user;
}

function vérifier_mdp_ldap(){
    return @ldap_bind($ldap, $user[0]['dn'], $password);
}

function get_user($username){
    #Connexion à la BD
    if(User::existe($username)){
        $id=User::get_user_id($username);
    }
    else{
        $id=User::creer_user($username);
    }
    $user_info=new User($id);

    return $user_info;
}

function get_infos_session($user_info){
    #Obtient les infos de l'utilisateur
    $_SESSION["nom"]=$user[0]['cn'][0];
    $_SESSION["user_id"]=$user_info->id;
    $_SESSION["username"]=$user_info->username;
    $_SESSION["actif"]=$user_info->actif;
    $_SESSION["role"]=$user_info->role;
}

function rediriger_apres_login(){
    if(!isset($_GET["p"])){
        header("Location: /index.php?p=accueil");
    }
    else{
        header("Location: /index.php?p=$_GET[p]&ID=$_GET[ID]");
    }
}

function login_sans_authentification(){
    $username=$_POST["username"];
    $user=get_user($username);
    get_infos_session($user);
    rediriger_apres_login();
}

function récupérer_infos(){
    $infos=array("erreur"=>$erreur,
                 "domaine_mail"=>$GLOBALS['config']['domaine_mail'],
                 "password"=>$GLOBALS['config']['auth_type']!="no"?"true":"");
}

function render_page($infos){
    $template=$GLOBALS['mustache']->loadTemplate("login");
    echo $template->render($infos);
}
