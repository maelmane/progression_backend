<?php

require_once('modele.php');

session_start();

function valid_signin(){
    $uname = $_POST['username'];
    $pw1 = $_POST['passwd'];
    $pw2 = $_POST['passwd2'];

    if(substr($uname,0,5)=='admin' || User::exist($uname))
        return "Le nom d'utilisateur est déjà utilisé";

    if(strlen($pw1)<8)
        return "Le mot de passe doit être d'au moins 8 caractères";

    if($pw1!=$pw2)
        return "Le mot de passe et sa confirmation ne correspondent pas";

    User::creer_user( $uname, $pw1 );

    $user_info=new User();
    $user_info->load_info($uname, $pw1);
    #Obtient les infos de l'utilisateur
    $_SESSION["user_id"]=$user_info->id;
    $_SESSION["username"]=$user_info->username;
    $_SESSION["active"]=$user_info->actif;

    return "";
    
}
             
if(isset($_SESSION["user_id"])){
    header('Location: index.php?p=accueil');
}
else{
    if(isset($_POST['submit'])){
        $erreur=valid_signin();
        if($erreur==""){
            header('Location: index.php?p=accueil');
        }
    }
}
echo'
   	  <html>
        <head>         
            <meta charset="utf-8">
	      <link rel="stylesheet" type="text/css" href="css/style.css">
              
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">     
            
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>    
            
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>             
              
	    </head>
	    <body id="imageBackground">
          <script>
            function valid_pw(){
              username=document.getElementById(\'username\').value;
              pw1=document.getElementById(\'passwd\').value;
              pw2=document.getElementById(\'passwd2\').value;
              if(username.length<1) return false;
              if(pw1!=pw2) return false;
              if(pw1.length<8) return false;
              return true;
            }
          </script>

          <section class="main">
		   <div class="example-wrapper clearfix">
         ' . $erreur .'
        <h1 id="inscription" align="center">Inscription</h1> 
        <div class="container" id="center">         
            <div class ="col-sm-offset-2">
                <form class="form-horizontal" id="signin" name="signin" method="POST">

                   <div class="form-group">             
                       <label id="inscription" class="control-label col-sm-2" for="username">Nom d\'utilisateur</label>
                     <div class="col-sm-3">
                       <input id="username" name="username" type="text" class="form-control" oninput="submit.disabled=!valid_pw();">
                     </div>
                   </div>

                   <div class="form-group">
                       <label id="inscription" class="control-label col-sm-2" for="passwd">Mot de passe</label>
                     <div class="col-sm-3">
                       <input id="passwd" name="passwd" type="password" class="form-control" oninput="submit.disabled=!valid_pw();">
                     </div>
                   </div>

                   <div class="form-group">
                       <label id="inscription" class="control-label col-sm-2" for="passwd2">Confirmation</label>
                     <div class="col-sm-3">
                       <input id="passwd2" name="passwd2" type="password" class="form-control"  oninput="submit.disabled=!valid_pw();">
                     </div>
                   </div>

                   <div class="form-group">
                     <div class="col-sm-offset-2 col-sm-10">
                       <input name="submit" type="submit" class="btn btn-primary" disabled value="S\'inscrire">
                     </div>
                   </div>
                </form>
            </div>
         </div>
               </div>
             </section>
    	    </body>
	    </html>
         ';
    
?>