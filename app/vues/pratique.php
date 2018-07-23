<?php

require('quiz_preambule.php');
db_init();

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

//Langage par défaut
$langid=QuestionProg::PYTHON3;

//Si un code a été soumis, l'insére dans la zone de texte, sinon utilise le code par défaut.
if (isset($_POST['langid'])&&$_POST['langid']!="") $langid=$_POST['langid'];
if (!isset($_POST['incode']) || $_POST['incode']==''){
    if($avcmt->reponse==''){
        $code='';
    }
    else{
        $code=$avcmt->reponse;
    }
}
else{
    $code=$_POST['incode'];
}

//Récupère les paramètres de compilation. Les paramètres provenant de la BD ont préscéance.
$params=$_POST['params'];

//Récupère les entrées à envoyer au programme. Les entrées provenant de la BD ont préscéance.
$stdin=trim($_POST['stdin']);

page_header();

echo"

  <html> 
   <head>
    <link rel='stylesheet' type='text/css' href='css/style.css'>
    <script src='./CodeMirror/lib/codemirror.js'></script>
    <link rel='stylesheet' href='./CodeMirror/lib/codemirror.css'>
";

if($langid<=QuestionProg::PYTHON3){
    echo"
    <script  src='./CodeMirror/mode/python/python.js'></script>
    ";
}
elseif($langid==QuestionProg::CPP){
    echo"
    <script  src='./CodeMirror/mode/clike/clike.js'></script>
    ";
}

echo "

    <meta charset='utf-8'>
   </head>
   <body>

     <section class='main'>
      <div class='example-wrapper clearfix'>
       <h3>Zone de pratique libre</h3>
           <br>
           Cette zone permet d'exécuter vos programmes dans le langage de votre choix. <br><em>Attention, vos programmes entrés ici ne seront pas sauvegardés.</em>
           <br>
           <br>
        <pre class='code-wrapper'><code><form method='post' action=''><table style='background-color: white; border-style:solid; border-color:black; border-width:0px; border-spacing: 10px 10px;'> 
     "; 

    echo " <tr>
       <td>Langage : ".menu_lang(langid>=0?$langid:1, false)."
       </td>
       </tr><tr>
       <td colspan=2>
        <textarea id='incode' name = 'incode' cols='80'>$code</textarea>
       </td>
      </tr>
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
   <input type=submit value='Soumettre' >
  </form>
 </code>
</pre>
";

//Log le code soumis
$com_log=$_SERVER['REMOTE_ADDR']." - " . $_SERVER["PHP_SELF"] . " : lang : " . $langid . " Code : ". $code;
syslog(LOG_INFO, $com_log);

//Compose le code à exécuter
$code_exec=preg_replace('~\R~u', "\n", $code);

//post le code à remotecompiler
$url_rc='http://' . $GLOBALS['config']['compilebox_hote'] . ':' . $GLOBALS['config']['compilebox_port'] .'/compile';
$data_rc=array('language' => $langid, 'code' => $code_exec, 'parameters' => "\"$params\"", 'stdin' => $stdin);
$options_rc=array('http'=> array(
    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
    'method'  => 'POST',
    'content' => http_build_query($data_rc)));

$context  = stream_context_create($options_rc);
$comp_resp=file_get_contents($url_rc, false, $context);
if ($comp_resp === FALSE) {
    $output="Erreur interne: " . $comp_resp;
} 
else{
    $output=trim(json_decode($comp_resp, true)['output']);
}

$errors=json_decode($comp_resp,true)['errors'];
if($errors!=""){
    echo "<br>Erreurs et avertissements : <br><pre class='code-wrapper'><code>$errors</code></pre><br>";
}


echo "<td align=right><a href=index.php?p=accueil>Retour à l'accueil</a></td></tr></table>
 <script>
    function betterTab(cm) {
      if (cm.somethingSelected()) {
        cm.indentSelection('add');
      } else {
        cm.replaceSelection(cm.getOption('indentWithTabs')? '\t':
          Array(cm.getOption('indentUnit') + 1).join(' '), 'end', '+input');
      }
    }
    ";

    echo " 
      var editor = CodeMirror.fromTextArea(document.getElementById('incode'),{
      matchBrackets: true,
      lineNumbers: true,
      readOnly: false,
      firstLineNumber: 1,
      indentUnit: 4,
      extraKeys: { Tab: betterTab }
      });
      editor.setSize(700);
      ";

//Affiche le pied de la question
echo "
    </script>
    </div>
  </body>
</html>
";

?>
