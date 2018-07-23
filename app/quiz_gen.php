    <?php
       function resume($in, $lignes_max){
       $lignes=explode("\n", $in);
       $nb_lignes=count($lignes);
           if ($nb_lignes<=$lignes_max){
               return $in;
           	}
           	else{
           			$av=round(($lignes_max-1)/2);
           			$ap=floor(($lignes_max-1)/2);
           			return implode("\n", array_merge(array_slice($lignes,0,$av),array("..."),array_slice($lignes,-$ap)));
           }		
				    
	}

       $locale='fr_CA.UTF-8';
       setlocale(LC_ALL,$locale);

       openlog("quiz",LOG_NDELAY, LOG_LOCAL0);

       //Exécute un script Python et vérifie la réponse.
       function execute( $titre, $question, $reponse="-----", $flag="", $pre_code="", $in_code="", $post_code="", $pre_exec="", $suivante=""){

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

       //Récupère les paramètres de compilation
       $params=$_POST['params'];

       //Récupère les entrées à envoyer au programme
       $stdin=trim($_POST['stdin']);

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
           echo " <tr><td colspan=2>
                            <textarea id='precode' rows='5' cols='80'>$pre_code</textarea>
                        </td>
                      </tr>";
       }
       echo " <tr><td colspan=2>
                            <textarea id='incode' name = 'incode' cols='80'>
$code</textarea>
                        </td>
                      </tr>";

       if ($post_code != ""){
           echo " <tr>
                        <td colspan=2>
                            <textarea id='postcode' rows='5' cols='80'>$post_code</textarea>
                        </td>
                      </tr>";
       }
       echo "
<tr>
<td width=50%>
<h3>Paramètres </h3>
<textarea id='params' name = 'params' rows='1' style='width:100%'>$params</textarea>

</td>
<td width=50%>
<h3>Entrées</h3>
<textarea id='stdin' name = 'stdin' rows='1' style='width:100%'>$stdin</textarea>

</td>
</tr>
 </table>
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

	//post le code à remotecompiler
        $url_rc='http://localhost:12380/compile';
        $data_rc=array('language' => $GLOBALS['lang_id'], 'code' => $code, 'parameters' => $params, 'stdin' => $stdin);
        $options_rc=array('http'=> array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data_rc)));

        $context  = stream_context_create($options_rc);
        $comp_resp= file_get_contents($url_rc, false, $context);
        if ($comp_resp === FALSE) {
             $output="Erreur interne: " . $comp_resp;
        } 
        else{
             $output=trim(json_decode($comp_resp, true)['output']);
        }
                                                   
        //Affiche le résultat
        if( $reponse!=""){
            echo "<br>Résultat attendu : <br><pre class='code-wrapper'><code>" . resume($reponse,21) . "</code></pre><br>";
        }
        echo "<br>Résultat observé : <br><pre class='code-wrapper'><code>" . resume($output,21) . "</code></pre><br>";

        $errors=json_decode($comp_resp,true)['errors'];
	if($errors!=""){
            echo "<br>Erreurs et avertissements : <br><pre class='code-wrapper'><code>$errors</code></pre><br>";
	}
        //Vérifie la réponse
        echo "<table width=100%><tr><td>";
        if ($reponse!="---"){
            if ($output==$reponse){
                echo "Bravo! " . ($flag=="" ? "":"La clé est «" . $flag . "»</td>");
                if ($suivante!=""){                                                             
                    echo "<td align=center><a href='$suivante'>Question suivante</a></td>";
                                                                                      }
                                                                                      }
                                                                                      
            else{
                echo "Essayez encore</td>";
            }
        }
        echo "<td align=right><a href='index.html'>Retour aux questions</a></td></tr></table>";

    echo ' <script>

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
      firstLineNumber: " . strval(substr_count($pre_exec, "\n") + 1)  . ",
      indentUnit: 4
      });    
     preeditor.setSize(null,'100%');//preeditor.getScrollInfo().height);
     ";}

     echo "
      var editor = CodeMirror.fromTextArea(document.getElementById('incode'),{
      matchBrackets: true,
      lineNumbers: true,
      readOnly: false,
      firstLineNumber: " . strval(substr_count($pre_exec, "\n") + substr_count($pre_code, "\n") + 1) . ",
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
    posteditor.setOption('firstLineNumber', " . strval(substr_count($pre_exec, "\n") + substr_count($pre_code, "\n")) . "+editor.doc.lineCount());     
    editor.setSize(null, editor.getScrollInfo().height);
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

