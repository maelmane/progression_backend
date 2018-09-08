<?php

require __DIR__ . '/vendor/autoload.php';
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/modele.php');
session_start();

$erreur="";

if(isset($_SESSION["user_id"])){
    header('Location: /index.php?p=accueil');
} else {
    load_config();
    if(isset($_POST["submit"])){
        
        if(empty($_POST["username"]) || empty($_POST["passwd"]) && $GLOBALS['config']['auth_type']!="no"){
            $erreur="Le nom d'utilisateur ou le mot de passe ne peuvent être vides.";
        }
        elseif($GLOBALS['config']['auth_type']=="local"){
            $erreur="L'authentification locale n'est pas implémentée.";
        }
        elseif($GLOBALS['config']['auth_type']=="ldap") {
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
            if($user['count']>0 && @ldap_bind($ldap, $user[0]['dn'], $password)){
                #Connexion à la BD
                if(User::existe($username)){
                    $id=User::get_user_id($username);
                }
                else{
                    $id=User::creer_user($username);
                }
                $user_info=new User($id);
                if($user_info->id){
                    #Obtient les infos de l'utilisateur
                    $_SESSION["nom"]=$user[0]['cn'][0];
                    $_SESSION["user_id"]=$user_info->id;
                    $_SESSION["username"]=$user_info->username;
                    $_SESSION["actif"]=$user_info->actif;
                    $_SESSION["role"]=$user_info->role;
                    if(!isset($_GET["p"])){
                        header("Location: /index.php?p=accueil");
                    }
                    else{
                        header("Location: /index.php?p=$_GET[p]&ID=$_GET[ID]");
                    }

                }
            }
            else {
                $erreur="Nom d'utilisateur ou mot de passe invalide.";
            }
        }
        elseif($GLOBALS['config']['auth_type']=="no"){
            $username=$_POST["username"];
            #Connexion à la BD
            if(User::existe($username)){
                $id=User::get_user_id($username);
            }
            else{
                $id=User::creer_user($username);
            }
                
            $user_info=new User($id);
            if($user_info->id){
                #Obtient les infos de l'utilisateur
                $_SESSION["nom"]=$user_info->username;
                $_SESSION["user_id"]=$user_info->id;
                $_SESSION["username"]=$user_info->username;
                $_SESSION["actif"]=$user_info->actif;
                $_SESSION["role"]=$user_info->role;
                if(!isset($_GET["p"])){
                    header("Location: /index.php?p=accueil");
                }
                else{
                    header("Location: login.php?p=$_GET[p]" . (isset($_GET['ID'])?("&ID=".$_GET['ID']):""));
                }
            }else {
                $erreur="Nom d'utilisateur invalide.";
            }
        }
    }

    $infos=array("erreur"=>$erreur,
                 "domaine_mail"=>$GLOBALS['config']['domaine_mail'],
                 "password"=>$GLOBALS['config']['auth_type']!="no"?"true":"");

    render_page($infos);
}

function render_page($infos){
    $template=$GLOBALS['mustache']->loadTemplate("login");
    echo $template->render($infos);
}
