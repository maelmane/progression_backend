<?php

require_once('modele.php');
session_start();

if(isset($_SESSION["user_id"])){
    header('Location: index.php?p=accueil');
}
else{
    load_config();
    
    if(isset($_POST["submit"])){
	$erreur="";
        if(empty($_POST["username"]) || empty($_POST["passwd"])){
            $erreur="Le nom d'utilisateur ou le mot de passe ne peuvent être vides.";
        }
        else
        {
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
                $erreur="Impossible de se connecter au serveur d'authentification. Veuillez communiquer avec l'administrateur du site. Erreur : $extender_error";
            }
            $result=ldap_search($ldap, $GLOBALS['config']['domaine_ldap'], "(sAMAccountName=$username)", array('dn','cn',1));
	    $user=ldap_get_entries($ldap, $result);
            if($user['count']>0 && @ldap_bind($ldap, $user[0]['dn'], $password)){
                #Connexion à la BD
                $user_info=new User(null, $username);
                if($user_info->load_info()){
                    #Obtient les infos de l'utilisateur
		    $_SESSION["nom"]=$user[0]['cn'][0];
                    $_SESSION["user_id"]=$user_info->id;
                    $_SESSION["username"]=$user_info->username;
                    $_SESSION["active"]=$user_info->actif;
                    if(!isset($_GET["p"])){
                        header("Location: index.php?p=accueil");
                    }
                    else{
                        header("Location: index.php?p=$_GET[p]&ID=$_GET[ID]");
                    }

                }
                else {
                    $erreur="Nom d'utilisateur ou mot de passe invalide.";
                }
            }
            else {
                $erreur="Nom d'utilisateur ou mot de passe invalide.";
            }
        }
    }
                
    if(isset($erreur)){
        echo "<div class='alert alert-danger'> $erreur </div>";
    }
    echo '
	  <html>
        <head>
              <meta charset="utf-8">
	      <link rel="stylesheet" type="text/css" href="css/style.css">

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	    </head>
	    <body id="login">

          <section class="main">
		      <div class="example-wrapper clearfix">
          <h1 id="loginTxt" style="text-align:center"> Bienvenue sur le site Quiz-Python </h1>
          <div class="container" id="centre">
            <form name="login" method="POST" class="form-horizontal">

              <div class="form-group">
                  <label id="loginTxt" class="control-label col-sm-3">Courriel : </label>
                  <div class="col-sm-3">
                    <input class="form-control" type="text" name="username" />
                  </div>
                  <div class="col-sm-3">
                    <label style="text-align:left;color:#888;">@'.$GLOBALS['config']['domaine_mail'].'</label>
                  </div>
             </div>
             <div class="form-group">
                  <label id="loginTxt"  class="control-label col-sm-3">Mot de passe : </label>
                  <div class="col-sm-3">
                    <input class="form-control" name="passwd" type="password"/>
                  </div>
              </div>

              <div class="col-sm-offset-3">
                <input name="submit" type="submit" class="btn btn-primary" value="Connexion">
<!-- Désactivé l\'autoinscription
                <a href="inscription.php">s\'inscrire</a>
-->
              </div>

            </form>
            </div>
          </div>
          </section>

    	    </body>
	    </html>
        ';
        }
