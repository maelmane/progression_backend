<?php

require_once('modele.php');

session_start();

if(!isset($_SESSION["user_id"])){
    header('Location: login.php');
}

$user=new User($_SESSION["username"]);
if(is_null($user->id))
{
    echo "Erreur interne : " . $db->errorno;
    die;
}

function page_header(){
    echo "
	  <html>
        <head>
              <meta charset='utf-8'>
	      <link rel='stylesheet' type='text/css' href='css/style.css'>
	    </head>
	    <body>
          <section class='main'>
		   <div class='example-wrapper clearfix'>

          <table width=100%><tr><td width=75% style='text-align:right;'><h1>" . $_SESSION["username"] . "</h1>(<a href='logout.php'>déconnexion</a>)</td></tr></table>

     ";
}

function page_footer(){
    echo"
               </div>
             </section>
    	    </body>
	    </html>
        ";


}
?>