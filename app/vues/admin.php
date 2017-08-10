<?php

require_once('quiz_preambule.php');

if($_SESSION['username']!='admin'){
    header("Location: login.php");
}

if($_POST['question']==-1){
    //Sauvegarde
    if($_POST['type']==Question::TYPE_PROG){
        $qst=new QuestionProg($_POST['id'], $_GET['serie'], $_POST['numero'], $_POST['titre'], $_POST['description'], $_POST['points'], $_POST['lang'], $_POST['setup'], $_POST['pre_exec'], $_POST['pre_code'], $_POST['incode'], $_POST['post_code'], $_POST['params'], $_POST['stdin']);
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
}

if(isset($_GET['serie'])){
    echo "
       <td><select id='question' name='question' onchange='load_question()'>
       <option value = 0 >Question</option>
       ";

    foreach($serie->get_questions() as $question){
        echo "<option value = $question->id ".($_GET['question']==$question->id?'selected':'').">".$question->titre."</option>";
    }
    echo "
       <option value=-1 ".($_GET['question']==-1?'selected':'').">Nouvelle question</option>
    ";

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
<option value=0>Programmation</option>
<option value=1>Terminal interactif</option>
</select>
</td>
</tr>

<tr>
<td>
Titre<br>
<input type=text id='titre' name='titre'   value='".$question->titre."'>
</td>
</tr>

<tr>
<td>
Description<br>
<input type=text id='description' name='description'    value='".$question->description."'>
</td>
</tr>


<tr>
<td>
Énoncé<br>
<textarea cols=80 id='enonce' name='enonce' >".$question->enonce."</textarea>
</td>
</tr>
</table>

<div  id='prog' name='prog' >

<table>
<tr>
<td>
Langage<br>".menu_lang($question->get_lang)."
</td>
</tr>


<tr>
<td>
Setup<br>
<textarea cols=80 id='setup' name='setup' >".$question->setup."</textarea>
</td>
</tr>

<tr>
<td>
Pré exécution<br>
<textarea cols=80 id='pre_exec' name='pre_exec' >".$question->pre_exec."</textarea>
</td>
</tr>

<tr>
<td>
Pré code<br>
<textarea cols=80 id='pre_code' name='pre_code' >".$question->pre_code."</textarea>
</td>
</tr>

<tr>
<td>
Code de base<br>
<textarea cols=80 id='in_code' name='in_code' >".$question->in_code."</textarea>
</td>
</tr>

<tr>
<td>
Post code<br>
<textarea cols=80 id='post_code' name='post_code' >".$question->post_code."</textarea>
</td>
</tr>

<tr>
<td>
Réponse<br>
<textarea cols=80 id='reponse' name='reponse' >".$question->reponse."</textarea>
</td>
</tr>

<tr>
<td>
Stdin<br>
<input type=text id='stdin' name='stdin'  value=".$question->stdin.">
</td>
</tr>

<tr>
<td>
Paramètres<br>
<input type=text id='params' name='params'  value=".$question->params.">
</td>
</tr>

</table>
</div>

<div id='sys' name='sys'  style='display: none;'>

<table>

<tr>
<td>
Image<br>
<input type=text id='image' name='image'  value=".$question->image.">
</td>
</tr>

<tr>
<td>
Nom d'utilisateur<br>
<input type=text id='username' name='username'  value=".$question->user.">
</td>
</tr>

<tr>
<td>
Réponse<br>
<input type=text id='reponse' name='reponse'   value=".$question->reponse.">
</td>
</tr>

<tr>
<td>
Validation<br>
<input type=text id='verification' name='verification'   value=".$question->verification.">
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