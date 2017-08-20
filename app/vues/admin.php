<?php

require_once('quiz_preambule.php');

if($_SESSION['username']!='admin'){
    header("Location: login.php");
}


//Sauvegarde
if(isset($_POST['question'])){
    if($_POST['type']==Question::TYPE_PROG){
        $qst=new QuestionProg($_POST['question'], $_GET['serie'], $_POST['numero'], $_POST['titre'], $_POST['description'], $_POST['enonce'], $_POST['reponse_prog'], $_POST['points'], $_POST['langid'], $_POST['setup'], $_POST['pre_exec'], $_POST['pre_code'], $_POST['incode'], $_POST['post_code'], $_POST['params'], $_POST['stdin']);

        $qid=$qst->save();
        header("Location: index.php?p=admin&theme=$_GET[theme]&serie=$_GET[serie]&question=$qid");
    }
    if($_POST['type']==Question::TYPE_SYS){
        $qst=new QuestionSysteme($_POST['question'], $_GET['serie'], $_POST['numero'], $_POST['titre'], $_POST['description'], $_POST['enonce'], $_POST['reponse_sys'], $_POST['points'], $_POST['image'], $_POST['username'], $_POST['verification']);

        $qid=$qst->save();
        header("Location: index.php?p=admin&theme=$_GET[theme]&serie=$_GET[serie]&question=$qid");
    }
}
    
    
page_header();

echo "

<script>
function toggletype(){
            document.getElementById('prog').style.display=(document.getElementById('type').value==0?'block':'none');
            document.getElementById('sys').style.display=(document.getElementById('type').value==1?'block':'none');
         }

function load_theme(){
         window.location='?p=admin&theme='+document.getElementById('theme').value;
}

function load_serie(){
         window.location='?p=admin&theme='+document.getElementById('theme').value+'&serie='+document.getElementById('serie').value;
}

function load_question(){
         window.location='?p=admin&theme='+document.getElementById('theme').value+'&serie='+document.getElementById('serie').value+'&question='+document.getElementById('question').value;}

</script>


<form method='post'>
<table border=0>
<th>Thème</th><th>Série</th><th>Question</th>
<tr>
<td><select id='theme' name='theme' onchange='load_theme()'>
<option value = 0 >Thème</option>
";

foreach(get_themes($_SESSION['user_id']) as $theme){
    echo "<option value = $theme->id ".($_GET['theme']==$theme->id?'selected':'').">$theme->titre</option>";
}

echo "</select></td>";

if(isset($_GET['theme'])){
    echo "</td>
       <td><select id='serie' name='serie' onchange='load_serie()'>
       <option value = 0 >Série</option>
       ";

    $theme=new Theme($_GET['theme'], $_SESSION['user_id']);
    $theme->load_info();

    foreach($theme->get_series() as $serie){
        echo "<option value = $serie->id ".($_GET['serie']==$serie->id?'selected':'').">$serie->titre</option>";
    }

    echo "</select></td>";
}

if(isset($_GET['serie'])){
    $serie=new Serie($_GET['serie'], $_SESSION['user_id']);
    $serie->load_info();

    echo "
       <td><select id='question' name='question' onchange='load_question()'>
       <option value = 0 >Question</option>
       ";

    foreach($serie->get_questions() as $question){
        echo "<option value = $question->id ".($_GET['question']==$question->id?'selected':'').">".$question->titre."</option>";
    }
    echo "
       <option value=-1 ".($_GET['question']==-1?'selected':'').">Nouvelle question</option></select></td>
    ";
}

if(isset($_GET['question'])){
    //Lien "visualiser"
    echo "<td><a href='index.php?p=".($question->type==Question::TYPE_PROG?"question_prog":"question_sys")."&ID=$_GET[question]' target='_blank'>visualiser</a></td>";
    
    $question=new Question($_GET['question']);
    if($_GET['question']!=-1){
        $question->load_info();

        if($question->type==Question::TYPE_PROG){
            $question=new QuestionProg($_GET['question']);
            $question->load_info();
        }
        elseif($question->type==Question::TYPE_SYS){
            $question=new QuestionSysteme($_GET['question']);
            $question->load_info();
        }
    }
}

echo"
</tr></table>";

if(isset($_GET['question'])){
    echo "
<table>
<tr>
<td>
Numéro<br>
<input type=text id='numero' name='numero'  value='".$question->numero."'>
</td>
<td>
Type<br>
<select id='type' name='type'  onchange='toggletype()'> 
<option value=0 ".($question->type==Question::TYPE_PROG?"selected":"").">Programmation</option>
<option value=1 ".($question->type==Question::TYPE_SYS?"selected":"").">Terminal interactif</option>
</select>
</td>
</tr>

<tr>
<td>
Titre<br>
<input type=text id='titre' name='titre'   value='".str_replace("'", "&#39;", $question->titre)."'>
</td>
</tr>

<tr>
<td>
Description<br>
<input type=text id='description' name='description'    value='".str_replace("'", "&#39;", $question->description)."'>
</td>
</tr>


<tr>
<td>
Énoncé (chaîne de caractères)<br>
<textarea cols=80 id='enonce' name='enonce' >".$question->enonce."</textarea>
</td>
</tr>
</table>

<div id='prog' name='prog' style='display:".($question->type==Question::TYPE_PROG?"block":"none")." '>

<table>
<tr>
<td>
Langage<br>".menu_lang($question->lang)."
</td>
</tr>


<tr>
<td>
Setup <img width=16 src='images/interrogation.png' title='script PHP exécuté avant le code soumis'><br>
<textarea cols=80 id='setup' name='setup' >".$question->setup."</textarea>
</td>
</tr>

<tr>
<td>
Pré exécution <img width=16 src='images/interrogation.png' title='expression PHP fournissant un code invisible à être exécuté au début code soumis'><br>
<textarea cols=80 id='pre_exec' name='pre_exec' >".$question->pre_exec."</textarea>
</td>
</tr>

<tr>
<td>
Pré code <img width=16 src='images/interrogation.png' title='expression PHP fournissant un code visible à être exécuté au début code soumis'><br>
<textarea cols=80 id='pre_code' name='pre_code' >".$question->pre_code."</textarea>
</td>
</tr>

<tr>
<td>
Code de base <img width=16 src='images/interrogation.png' title='chaîne de caractères placée dans la zone modifiable de la question'><br>
<textarea cols=80 id='incode' name='incode' >".$question->incode."</textarea>
</td>
</tr>

<tr>
<td>
Post code <img width=16 src='images/interrogation.png' title='expression PHP  fournissant un code visible à être exécuté à la fin du code soumis'><br>
<textarea cols=80 id='post_code' name='post_code' >".$question->post_code."</textarea>
</td>
</tr>

<tr>
<td>
Réponse <img width=16 src='images/interrogation.png' title='expression PHP donnant la réponse ou «null» si la réponse doit être une chaîne vide'><br>
<textarea cols=80 id='reponse_prog' name='reponse_prog' >".$question->reponse."</textarea>
</td>
</tr>

<tr>
<td>
Stdin <img width=16 src='images/interrogation.png' title='chaîne de caractères passé à l&#39;entrée standard lors de l&#39;exécution du code soumis'><br>
<input type=text id='stdin' name='stdin'  value='".str_replace("'", "&#39;", $question->stdin)."'>
</td>
</tr>

<tr>
<td>
Paramètres <img width=16 src='images/interrogation.png' title='chaîne de caractères passé en paramètres sur la ligne de commande lors de l&#39;exécution du code soumis'><br>
<input type=text id='params' name='params'  value='".str_replace("'", "&#39;", $question->params)."'>
</td>
</tr>

</table>
</div>

<div id='sys' name='sys'  style='display:".($question->type==Question::TYPE_SYS?"block":"none")." '>

<table>

<tr>
<td>
Image<br>
<input type=text id='image' name='image'  value='".str_replace("'", "&#39;", $question->image)."'>
</td>
</tr>

<tr>
<td>
Nom d'utilisateur<br>
<input type=text id='username' name='username'  value='".str_replace("'", "&#39;", $question->user)."'>
</td>
</tr>

<tr>
<td>
Réponse<br>
<input type=text id='reponse_sys' name='reponse_sys'   value='".str_replace("'", "&#39;", $question->reponse)."'>
</td>
</tr>

<tr>
<td>
Validation<br>
<input type=text id='verification' name='verification'   value='".str_replace("'", "&#39;", $question->verification)."'>
</td>
</tr>
";
}

echo"


</table>
</div>
<input type=submit value='Enregistrer'>
</form>
";

?>