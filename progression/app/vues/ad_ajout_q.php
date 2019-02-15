<?php

require_once('prog.php');
include('admin.php');

function render_page(){
    if(isset($_POST['submit'])){
        sauvegarder();
    }
    afficher_champs();
}

function sauvegarder(){
    //Sauvegarde
    if($_POST['type']==Question::TYPE_PROG){
        $qst=new QuestionProg($_POST['question']);
        $qst->actif=$_POST['actif'];
        $qst->type=Question::TYPE_PROG;
        $qst->serieID =$_GET['serie'];
        $qst->numero =$_POST['numero'];
        $qst->titre =$_POST['titre'];
        $qst->description =$_POST['description'];
        $qst->enonce =$_POST['enonce'];
        $qst->solution =$_POST['solution'];
        $qst->code_validation =$_POST['code_validation'];
        $qst->langid =$_POST['langid'];
        $qst->setup =$_POST['setup'];
        $qst->pre_exec =$_POST['pre_exec'];
        $qst->pre_code =$_POST['pre_code'];
        $qst->incode =$_POST['incode'];
        $qst->post_code =$_POST['post_code'];
        $qst->params =$_POST['params'];
        $qst->stdin =$_POST['stdin'];
        
        $qid=$qst->save();
        header("Location: index.php?p=ad_ajout_q&theme=$_GET[theme]&serie=$_GET[serie]&question=$qid");
    }
    if($_POST['type']==Question::TYPE_SYS){
        $qst=new QuestionSysteme($_POST['question']);
        $qst->actif=$_POST['actif'];
        $qst->type=Question::TYPE_SYS;
        $qst->serieID = $_GET['serie'];
        $qst->numero=$_POST['numero'];
        $qst->titre=$_POST['titre'];
        $qst->description=$_POST['description'];
        $qst->enonce=$_POST['enonce'];
        $qst->solution_courte=$_POST['solution_courte'];
        $qst->code_validation=$_POST['code_validation'];
        $qst->image=$_POST['image'];
        $qst->user=$_POST['username'];
        $qst->verification=$_POST['verification'];
        
        $qid=$qst->save();
        header("Location: index.php?p=ad_ajout_q&theme=$_GET[theme]&serie=$_GET[serie]&question=$qid");
    }

    if($_POST['type']==Question::TYPE_BD){
        
        $qst=new QuestionBD($_POST['question']);
        $qst->actif=$_POST['actif'];
        $qst->type=Question::TYPE_BD;
        $qst->serieID =$_GET['serie'];
        $qst->numero =$_POST['numero'];
        $qst->titre =$_POST['titre'];
        $qst->description =$_POST['description'];
        $qst->enonce =$_POST['enonce'];
        $qst->solution =$_POST['solution'];
        $qst->solution_courte =$_POST['solution_courte'];        
        $qst->code_validation =$_POST['code_validation'];
        $qst->langid =$_POST['langid'];
        $qst->setup =$_POST['setup'];
        $qst->pre_exec =$_POST['pre_exec'];
        $qst->pre_code =$_POST['pre_code'];
        $qst->incode =$_POST['incode'];
        $qst->post_code =$_POST['post_code'];
        $qst->params =$_POST['params'];
        $qst->stdin =$_POST['stdin'];

        $qst->image=$_POST['image'];
        $qst->user=$_POST['username'];
        $qst->verification=$_POST['verification'];
        
        $qid=$qst->save();
        header("Location: index.php?p=ad_ajout_q&theme=$_GET[theme]&serie=$_GET[serie]&question=$qid");
    }
}
function afficher_champs(){
    echo "

<script>
function toggletype(){
            document.getElementById('prog').style.display=(document.getElementById('type').value!=1?'block':'none');
            document.getElementById('sys').style.display=(document.getElementById('type').value!=0?'block':'none');
         }

function load_theme(){
         window.location='?p=ad_ajout_q&theme='+document.getElementById('theme').value;
}

function load_serie(){
         window.location='?p=ad_ajout_q&theme='+document.getElementById('theme').value+'&serie='+document.getElementById('serie').value;
}

function load_question(){
         window.location='?p=ad_ajout_q&theme='+document.getElementById('theme').value+'&serie='+document.getElementById('serie').value+'&question='+document.getElementById('question').value;}

</script>


<form method='post' id='fquestion'>
<table border=0>
<th>Thème</th><th>Série</th><th>Question</th>
<tr>
<td><select id='theme' name='theme' onchange='load_theme()'>
<option value = 0 >Thème</option>
    ";

    foreach(get_themes(true) as $theme){
        echo "<option value = $theme->id ".(isset($_GET['theme']) && $_GET['theme']==$theme->id?'selected':'').">$theme->titre</option>";
    }

    echo "</select></td>";

    if(isset($_GET['theme'])){
        echo "</td>
       <td><select id='serie' name='serie' onchange='load_serie()'>
       <option value = 0 >Série</option>
        ";

        $theme=new Theme($_GET['theme']);

        foreach($theme->get_series(true) as $serie){
            echo "<option value = $serie->id ".(isset($_GET['serie']) && $_GET['serie']==$serie->id?'selected':'').">$serie->titre</option>";
        }

        echo "</select></td>";
    }

    if(isset($_GET['serie'])){
        $serie=new Serie($_GET['serie']);

        echo "
       <td><select id='question' name='question' onchange='load_question()'>
       <option value = 0 >Question</option>
        ";

        foreach($serie->get_questions(true) as $question){
            echo "<option value = $question->id ".(isset($_GET['question']) && $_GET['question']==$question->id?'selected':'').">".$question->numero." " .$question->titre."</option>";
        }
        echo "
       <option value=-1 ".((isset($_GET['question']) && $_GET['question']==-1)?'selected':'').">Nouvelle question</option></select></td>
       ";
    }

    if(isset($_GET['question'])){
        //Lien "visualiser"
        if($question->type==Question::TYPE_PROG)
            echo "<td><a href='index.php?p=question_prog";
        else if($question->type==Question::TYPE_SYS)
            echo "<td><a href='index.php?p=question_sys";
        else if($question->type==Question::TYPE_BD)
            echo "<td><a href='index.php?p=question_bd";

        echo "&ID=$_GET[question]' target='_blank'>visualiser</a></td>";
        
        if($_GET['question']!=-1){
            $question=new Question($_GET['question']);

            if($question->type==Question::TYPE_PROG){
                $question=new QuestionProg($_GET['question']);
            }
            elseif($question->type==Question::TYPE_SYS){
                $question=new QuestionSysteme($_GET['question']);
            }
            elseif($question->type==Question::TYPE_BD){
                $question=new QuestionBD($_GET['question']);
            }
            else{
                $question=new QuestionProg(-1);
            }
        }

        echo"
</tr></table>";
    }
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
<select id='type' name='type'  onchange='toggletype()' > 
<option value=0 ".($question->type==Question::TYPE_PROG?"selected":"").">Programmation</option>
<option value=1 ".($question->type==Question::TYPE_SYS?"selected":"").">Terminal interactif</option>
<option value=2 ".($question->type==Question::TYPE_BD?"selected":"").">Base de données</option>
</select>
</td>
<td>
Actif<br>
<select id='actif' name='actif'  onchange='toggletype()' > 
<option value=0 ".($question->actif==0?"selected":"").">Inactive</option>
<option value=1 ".($question->actif==1?"selected":"").">Active</option>
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

<tr>
<td>
Code de validation<br>
<input type=text id='code_validation' name='code_validation'   value='".str_replace("'", "&#39;", $question->code_validation)."'>
</td>
</tr>
</table>

<div id='prog' name='prog' style='display:".(($question->type==Question::TYPE_PROG || $question->type==Question::TYPE_BD)?"block":"none")." '>

<table>
<tr>
<td>
Langage<br>".menu_lang($question->lang, true)."
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
<textarea cols=80 id='solution' name='solution' >".$question->solution."</textarea>
</td>
</tr>

<tr>
<td>
Stdin <img width=16 src='images/interrogation.png' title='expression PHP donnant une chaîne de caractères passée à l&#39;entrée standard lors de l&#39;exécution du code soumis'><br>
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

<div id='sys' name='sys'  style='display:".(($question->type==Question::TYPE_SYS || $question->type==Question::TYPE_BD)?"block":"none")." '>

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
<input type=text id='solution_courte' name='solution_courte'   value='".str_replace("'", "&#39;", $question->solution_courte)."'>
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
<input type=submit name='submit' value='Enregistrer'>
</form>
    ";
}
?>
