<?php

session_start();

if(!isset($_SESSION["login_user"])){
    header('Location: login.php');
}
else{
    echo "
	  <html> <head>
              <meta charset='utf-8'>
	      <link rel='stylesheet' type='text/css' href='css/style.css'>
	    </head>
	    <body>
	      <section class='main'>
		<div class='example-wrapper clearfix'>
		  <h3>Quiz de programmation</h3>

          <h1>Bienvenue " . $_SESSION["login_user"] . "</h1>
		  <pre class='code-wrapper'><code>
		      <table>
			<tr>
			  <td><a href='python/index.html'>Quiz en Python</a></td>
			</tr>
			<tr>
			  <td><a href='cpp/index.html'>Quiz en C/C++</a></td>
			</tr>
		      </table>
		</div>
	      </section>
	    </body>
	  </html>

    ";
}

?>