<html> <head>
  <link rel="stylesheet" type="text/css" href="/quiz/css/style.css">
    <script src="/quiz/js/codemirror/lib/codemirror.js"></script>
    <link rel="stylesheet" href="/quiz/js/codemirror/lib/codemirror.css">
    <script  src="/quiz/js/codemirror/mode/python/python.js"></script>
  </head>
  <body>

    <?php
       $locale='fr_CA.UTF-8';
       setlocale(LC_ALL,$locale);
       putenv('LC_ALL='.$locale);

       openlog("quiz",LOG_NDELAY, LOG_LOCAL0);

       //Exécute un script Python et vérifie la réponse.
       function execute( $titre, $question, $reponse, $flag="", $pre_code="", $in_code="", $post_code="", $pre_exec="", $suivante=""){

       //Trouve la question suivante si elle existe
       if ($suivante==""){
           preg_match_all('/q([0-9]+)\.php/', $_SERVER["PHP_SELF"], $page_cour);
           $suivante="q" . (string)($page_cour[1][0]+1) . ".php";
           $suivante=(file_exists($suivante)?$suivante:"");
       }
       
       //Si un code a été soumis, l'insére dans la zone de texte, sinon utilise le code par défaut.
       if ($_POST['incode']!=''){
         $code=$_POST['incode'];
       }
       else{
         $code=$in_code;
       }


       //Fonctions de remplacement

       //remplace le . par X
       //$code = str_replace(".", "X", $code);
       //$code = str_replace("strX", "str.", $code);

       //Affiche le formulaire
       echo"
            <section class='main'>
	      <div class='example-wrapper clearfix'>
		<h3>$titre</h3><br>$question<br><br>
		<pre class='code-wrapper'><code><form method='post' action=''><table style='background-color: white; border-style:solid; border-color:black; border-width:0px; border-spacing: 10px 10px;'> "; 

       if ($pre_code != ""){
           echo " <tr><td>
                            <textarea id='precode' rows='5' cols='80'>$pre_code</textarea>
                        </td>
                      </tr>";
       }
       echo " <tr><td>
                            <textarea id='incode' name = 'incode' rows='5' cols='80'>
$code</textarea>
                        </td>
                      </tr>";

       if ($post_code != ""){
           echo " <tr>
                        <td>
                            <textarea id='postcode' rows='5' cols='80'>$post_code</textarea>
                        </td>
                      </tr>";
       }
       echo " </table>
                    <input type=submit value='Soumettre'>
                    </form>

                 </code>
                 </pre>
                    ";


	//Log le code soumis
	$com_log=$_SERVER['REMOTE_ADDR']." - " . $_SERVER["PHP_SELF"] . " : ". $code;
	syslog(LOG_INFO, $com_log);

        //Compose le code à exécuter
        if ($pre_code != ""){ $pre_code = $pre_code . "\n"; }
        $code=$pre_exec. $pre_code .  $code . $post_code;
	$temp_f=tempnam("/opt/pyjail/tmp/","");
        chmod($temp_f, 644);
	file_put_contents($temp_f, $code);

        //exécution
        $command = "2>&1 sudo -nu quiz /usr/bin/jailed-python " . substr($temp_f,11); //<<EOF\n$code\nEOF";

//        //Affiche les avertissements
//        file_put_contents("/tmp/test.py","$precode\n$code\n$post_code");
//        $output = shell_exec("cat /dev/stdin <<EOF $precode\n$code\n$post_code\nEOF\n\n>/tmp/res");
//
//        echo "<br>Avertissements : <br><pre class='code-wrapper'><code>$output\n</code></pre><br></pre><br>";
                                                   
        //Affiche le résultat
        $output = trim(shell_exec($command));
	unlink($temp_f);
        echo "<br>Résultat attendu : <br><pre class='code-wrapper'><code>" . $reponse . "</code></pre><br>";
        echo "<br>Résultat observé : <br><pre class='code-wrapper'><code>" . $output . "</code></pre><br>";

        //Vérifie la réponse
        echo "<table width=100%><tr><td>";
        if ($output==trim($reponse)){
            echo "Bravo! " . ($flag=="" ? "":"La clé est «" . $flag . "»</td>");
            if ($suivante!=""){                                                             
                echo "<td align=center><a href='$suivante'>Question suivante</a></td>";
                                                                                  }
                                                                                  }
                                                                                  
        else{
            echo "Essayez encore</td>";
        }
        echo "<td align=right><a href='index.html'>Retour aux questions</a></td></tr></table>";

    echo '<script>
    function betterTab(cm) {
      if (cm.somethingSelected()) {
        cm.indentSelection("add");
      } else {
        cm.replaceSelection(cm.getOption("indentWithTabs")? "\t":
          Array(cm.getOption("indentUnit") + 1).join(" "), "end", "+input");
      }
    }
    ';

    if ($pre_code != ""){
     echo " 
      var preeditor = CodeMirror.fromTextArea(document.getElementById('precode'),{
      readOnly: true,
      lineNumbers: true,
      firstLineNumber: " . strval(substr_count($pre_exec, "\n")+2)  . ",
      indentUnit: 4
      });    
     ";}

     echo "
      var editor = CodeMirror.fromTextArea(document.getElementById('incode'),{
      matchBrackets: true,
      lineNumbers: true,
      readOnly: false,
      firstLineNumber: " . strval(substr_count($pre_exec, "\n") + substr_count($pre_code, "\n")+1) . ",
      indentUnit: 4,
      extraKeys: { Tab: betterTab }
      });
      editor.setSize(700);
     ";

     if ($post_code !=""){
      echo "
      var posteditor = CodeMirror.fromTextArea(document.getElementById('postcode'),{
      readOnly: true,
      lineNumbers: true,
      firstLineNumber: " . strval(substr_count($pre_exec, "\n") + substr_count($pre_code, "\n")) . "+editor.doc.lineCount()+1,      
      indentUnit: 4
      });
     

    editor.doc.on('change', function(instance, changeObj){
    posteditor.setOption('firstLineNumber', " . strval(substr_count($pre_exec, "\n") + substr_count($pre_code, "\n")) . "+editor.doc.lineCount()+2);     

    });
      ";}

        //Affiche le pied de la question
    echo "
    </script>
    </div>
  </body>
</html>
";
    }

closelog();
?>

