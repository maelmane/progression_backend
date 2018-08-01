<?php

require('quiz_preambule.php');
require('prog.php');

$infos=array("langid"=>get_lang_defaut(),
             "code"=>get_code(),
             "params"=>get_params(),
             "stdin"=>get_stdin());

page_header();
prog_header($infos['langid']); //Attention! deuxième section <head>
page_contenu($infos);
prog_footer();
page_footer();

function prog_header($langid){
    inclusions_codemiror();
    set_mode_éditeur($langid);
    prog_header_fermeture();
}

function page_contenu($infos){
    afficher_intro();
    afficher_formulaire($infos);
    afficher_résultats($infos);
}

function inclusions_codemiror(){
    echo"<html> 
          <head>
              <link rel='stylesheet' type='text/css' href='css/style.css'>
              <script src='./CodeMirror/lib/codemirror.js'></script>
              <link rel='stylesheet' href='./CodeMirror/lib/codemirror.css'>";
}

function set_mode_éditeur($langid){
    if($langid<=QuestionProg::PYTHON3){
        echo "<script src='./CodeMirror/mode/python/python.js'></script>";
    }
    elseif($langid==QuestionProg::CPP || $langid==QuestionProg::JAVA){
        echo "<script src='./CodeMirror/mode/clike/clike.js'></script>";
    }
}

function prog_header_fermeture(){
       echo "    <meta charset='utf-8'>
          </head>";
}

function afficher_intro(){
    echo "<body>
             <section class='main'>
                 <div class='example-wrapper clearfix'>
                     <h3>Zone de pratique libre</h3>
                     <br>
                     Cette zone permet d'exécuter vos programmes dans le langage de votre choix. 
                     <br>
                     <em>Attention, vos programmes entrés ici ne seront pas sauvegardés.</em>
                     <br>
                     <br>
                     <pre class='code-wrapper'>
                         <code>";
}

function afficher_formulaire($infos){
    echo "                   <form method='post' action=''>
                                 <table style='background-color: white; border-style:solid; border-color:black; border-width:0px; border-spacing: 10px 10px;'> 
                                     <tr>
                                         <td>
                                             Langage : ".menu_lang($infos['langid']>=0?$infos['langid']:1, false)."
                                         </td>
                                     </tr><tr>
                                         <td colspan=2>
                                             <textarea id='incode' name = 'incode' cols='80'>".$infos['code']."</textarea>
                                         </td>
                                     </tr>
                                     <tr>
                                         <td width=50%>
                                             <h3>Paramètres </h3>
                                             <textarea id='params' name = 'params' rows='1' style='width:100%'>".$infos['params']."</textarea>
                                         </td>
                                         <td width=50%>
                                             <h3>Entrées</h3>
                                             <textarea id='stdin' name = 'stdin' rows='1' style='width:100%'>".$infos['stdin']."</textarea>
                                         </td>
                                     </tr>
                                 </table>
                                 <input type=submit value='Soumettre' >
                             </form>
                         </code>
                     </pre>";
}

function afficher_sortie_standard($output){
    echo "           <br>Sortie standard : <br><pre class='code-wrapper'><code>".resume($output,21)."</code></pre><br>";
}

function afficher_sortie_erreur($erreurs){
    if($erreurs!=""){
        echo "       <br>Sortie d'erreur : <br><pre class='code-wrapper'><code>$erreurs</code></pre><br>";
    }
}


function prog_footer(){
    echo "           <a href=index.php?p=accueil>↩ Retour à l'accueil</a>
                     <script>
                        function betterTab(cm) {
                          if (cm.somethingSelected()) {
                            cm.indentSelection('add');
                          } else {
                            cm.replaceSelection(cm.getOption('indentWithTabs')? '\t':
                              Array(cm.getOption('indentUnit') + 1).join(' '), 'end', '+input');
                          }
                        }
                          var editor = CodeMirror.fromTextArea(document.getElementById('incode'),{
                          matchBrackets: true,
                          lineNumbers: true,
                          readOnly: false,
                          firstLineNumber: 1,
                          indentUnit: 4,
                          extraKeys: { Tab: betterTab }
                          });
                          editor.setSize(700);                 
                     </script>
                 </div>
             </section>";
}

function afficher_résultats($infos){
    $sorties=executer_code($infos);
    afficher_sorties($sorties);
}

function executer_code($infos){
    loguer_code($infos);

    //Extrait les infos
    $langid=$infos["langid"];
    $code=$infos["code"];
    $params=$infos["params"];
    $stdin=$infos["stdin"];

    //Compose le code à exécuter
    $code_exec=preg_replace('~\R~u', "\n", $code);

    //post le code à remotecompiler
    $url_rc='http://' . $GLOBALS['config']['compilebox_hote'] . ':' . $GLOBALS['config']['compilebox_port'] .'/compile';
    $data_rc=array('language' => $langid, 'code' => $code_exec, 'parameters' => "\"$params\"", 'stdin' => $stdin);
    $options_rc=array('http'=> array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data_rc)));
    $context=stream_context_create($options_rc);
    $comp_resp=file_get_contents($url_rc, false, $context);

    return $comp_resp;
}

function loguer_code($infos){
    $com_log=$_SERVER['REMOTE_ADDR']." - " . $_SERVER["PHP_SELF"] . " : lang : " . $infos['langid'] . " Code : ". $infos['code'];
    syslog(LOG_INFO, $com_log);
}

function afficher_sorties($sorties){
    if ($sorties === FALSE) {
        $output="Erreur interne. ";
    }
    else{
        $output=trim(json_decode($sorties, true)['output']);
        $erreurs=json_decode($sorties, true)['errors'];
    }
    afficher_sortie_standard($output);
    afficher_sortie_erreur($erreurs);
}

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
