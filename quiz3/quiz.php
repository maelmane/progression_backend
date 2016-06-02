<html> <head>
  <link rel="stylesheet" type="text/css" href="../css/style.css">
  </head>
  <body onload="init()">

    <script type="text/javascript">
      var observe;
      if (window.attachEvent) {
      observe = function (element, event, handler) {
      element.attachEvent('on'+event, handler);
      };
      }
      else {
      observe = function (element, event, handler) {
      element.addEventListener(event, handler, false);
      };
      }
      function init () {
      var text = document.getElementById('code');
      function resize () {
      text.style.height = 'auto';
      text.style.height = text.scrollHeight+'px';
      }
      /* 0-timeout to get the already changed text */
      function delayedResize () {
      window.setTimeout(resize, 0);
      }
      observe(text, 'change',  resize);
      observe(text, 'cut',     delayedResize);
      observe(text, 'paste',   delayedResize);
      observe(text, 'drop',    delayedResize);
      observe(text, 'keydown', delayedResize);

      text.focus();
      text.select();
      resize();
      }
    </script>
    <?php
       mb_internal_encoding("utf8");
       //Exécute un script Python et vérifie la réponse.
       function execute( $titre, $question, $reponse, $flag="", $pre_code="", $in_code="", $post_code="", $prep_code=""){

       //Si un code a été soumis, l'insére dans la zone de texte, sinon utilise le code par défaut.
       if ($_POST['code']!=''){
       $code=$_POST['code'];
       }
       else{
       $code=$in_code;
       }


       //Fonctions de remplacement

       //remplace le . par X
       $code = str_replace("\.", "X", $code);

       //Affiche le formulaire
       echo"
            <section class='main'>
	      <div class='example-wrapper clearfix'>
		<h3>$titre</h3>
                    $question
		<pre class='code-wrapper'><code>
                    <form method='post' action=''>
                    <table style='background-color: white; border-style:solid; border-color:black;'>
                      <tr>
                        <td>
                          <pre style='background-color:#E8E8E8'>$pre_code</pre>
                            <textarea id='code' rows=5 cols=80 name='code' style='background-color: transparent; border-style:none'>$code</textarea>
                            <pre style='background-color:#E8E8E8'>$post_code</pre>
                        </td>
                      </tr>
                    </table>
                    <input type=submit value='Soumettre'>
                    </form>
                    ";

        //Compose le code à exécuter
        $code = str_replace('"','\"',$pre_code . "\n" . $code . "\n" . $post_code);

        //exécution
        $pre_exec="#encoding: utf-8\n__builtins__.__import__=None;__builtins__.eval=None;__builtins__.exec=None;__builtins__.open=None;" . str_replace('"','\"',$prep_code);
        $command = "2>&1 python3 -c \"$pre_exec\n$code\"";

        //Affiche le résultat
        $output = trim(shell_exec($command));
        echo "<br>Résultat : <br>" . $output . "<br>";

        //Vérifie la réponse
        if ($output==$reponse){
            echo "Bravo!" . ($flag=="" ? "":"La clé est «" . $flag . "»");
        }
        else{
            echo "Essayez encore";
        }
        //Affiche le pied de la question
    echo"
    </code>
    </pre>
    <a href='index.html'>Retour au sommaire du quiz</a>
    </div>
  </body>
</html>
";
    }


?>

