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
	    </head>
	    <body>
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

         <form id="signin" name="signin" method="POST">

             <table style="margin-left:auto;margin-right:auto;">
             <tr>
             <td>Nom d\'utilisateur</td><td><input name="username"  id="username" type="text"  oninput="submit.disabled=!valid_pw();"></td>
             </tr><tr>
             <td>Mot de passe</td><td><input id="passwd" name="passwd" type="password"  oninput="submit.disabled=!valid_pw();"></td>
             </tr><tr>
             <td>Confirmation</td><td><input id="passwd2" name="passwd2" type="password"  oninput="submit.disabled=!valid_pw();"></td>
             </tr><tr>
             <td></td><td><input name="submit" type="submit" disabled value="S\'inscrire"></td>
             </tr>
         </form>
               </div>
             </section>
    	    </body>
	    </html>
         ';
    
?>