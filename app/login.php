<?php

require_once('modele.php');
session_start();

if(isset($_SESSION["user_id"])){
    header('Location: index.php?p=accueil');
}
else{

    if(isset($_POST["submit"])){
        if(empty($_POST["username"]) || empty($_POST["passwd"])){
            $erreur="Le nom d'utilisateur ou le mot de passe ne peuvent être vides.";
        }
        else
        {
            $username=$_POST["username"];
            $password=$_POST["passwd"];

            #            $ldap = ldap_connect("localhost") or die("Impossible de se connecter au serveur d'authentification. Veuillez contacter l'administrateur");
            #            ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
            #            $bind = @ldap_bind($ldap, "cn=$username,dc=local", "$password");
            #            if($bind) {

            #Connexion à la BD
            $user_info=new User();
            if($user_info->load_info($username, $password)){
            #Obtient les infos de l'utilisateur
            $_SESSION["user_id"]=$user_info->id;
            $_SESSION["username"]=$user_info->username;
            $_SESSION["active"]=$user_info->actif;
            if(!isset($_GET["p"]))
                header("Location: index.php?p=accueil");
            else
                header("Location: index.php?p=$_GET[p]&ID=$_GET[ID]");

        }
            else {
            $erreur="Nom d'utilisateur ou mot de passe invalide.";
        }
        }
        }
            if($erreur){
            echo $erreur;
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
                  <label id="loginTxt" class="control-label col-sm-3">Nom d\'utilisateur : </label>
                  <div class="col-sm-3">
                    <input class="form-control" type="text" name="username"/>
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
                <a href="inscription.php">s\'inscrire</a>
              </div>
            </form>
            </div>
          </div>
          </section>

    	    </body>
	    </html>
        ';
        }
