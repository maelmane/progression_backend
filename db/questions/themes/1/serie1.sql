INSERT INTO serie (themeID, serieID, numero, titre, description) VALUES (1, 1, 1, "Les expressions et les entrées/sorties", "Exercices de base sur les expressions et les opérations de saisie et d\'affichage");

    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 1, 1,'Question 0', 'Question 0', 'Faites afficher «Bonjour le monde!».');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), '"Bonjour le monde!\n"', '$r=rand(0,1000); ', '', '', 'print(42)', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 2, 1,'Question 1', 'Question 1', 'Faites afficher la somme des nombres alpha et beta.');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), '"\$somme\n"', '$r=rand(0,1000); $s=rand(0,1000); $somme=$r+$s; ', '', '"alpha=\$r\nbeta=\$s"', 'print(42)', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 3, 1,'Question 2', 'Question 2', 'Faites afficher le double de la variable alpha.');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), '"\$reponse\n"', '$r=rand(0,1000); $reponse=2*$r; ', '', '"alpha=\$r"', 'print(42)', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 4, 1,'Question 3', 'Question 3', 'Faites afficher la phrase «La somme de \$r et \$s est \$reponse ».');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), '"La somme de \$r et \$s est \$reponse\n"', '$r=rand(0,1000); $s=rand(0,1000); $reponse=$r+$s; ', '', '"alpha=\$r\nbeta=\$s"', 'print(42)', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 5, 1,'Question 4', 'Question 4', 'Faites afficher l\'entrée saisie au clavier.');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), '"\$entree[0]\n"', '$entree=explode("\r\n",$_POST["stdin"]); if($entree[0]==0){   $entree[0]=rand(0,999); } if($entree[1]==0){   $entree[1]=rand(0,999); }  if($_POST[\'stdin\']==\'\') $_POST[\'stdin\']=$entree[0]; ', '', '', 'print(42)', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 6, 1,'Question 5', 'Question 5', 'Répétez sur trois lignes l\'entrée saisie au clavier.');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), '"\$entree[0]\n\$entree[0]\n\$entree[0]\n"', '$entree=explode("\r\n",$_POST["stdin"]); if($entree[0]==\'\'){   $entree[0]=rand(0,999); }  if($_POST[\'stdin\']==\'\') $_POST[\'stdin\']=$entree[0];  ', '', '', 'print(42)', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 7, 1,'Question 6', 'Question 6', 'Faites afficher le double de la valeur numérique saisie au clavier.');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), 'intval(\$entree[0])*2 . "\n"', '$entree=explode("\n",$_POST["stdin"]); if($entree[0]==\'\'){   $entree[0]=rand(0,999); }  if($_POST[\'stdin\']==\'\') $_POST[\'stdin\']=$entree[0];   ', '', '', 'print(42)', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 8, 1,'Question 7', 'Question 7', 'Faites afficher la somme du double de la première valeur numérique saisie au clavier et de trois fois la deuxième.');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), 'intval(\$entree[0])*2+intval(\$entree[1])*3 . "\n"', '$entree=explode("\r\n",$_POST["stdin"]); if($entree[0]==0){   $entree[0]=rand(0,999); } if($entree[1]==0){   $entree[1]=rand(0,999); }  if($_POST[\'stdin\']==\'\') $_POST[\'stdin\']="$entree[0]\r\n$entree[1]";  ', '', '', 'print(42)', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 9, 1,'Question 8', 'Question 8', 'Faites afficher le résultat de la somme des entrées sous la forme «entrée1 + entrée2 = résultat» (par exemple «7 + 3 = 10»).');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), '\$entree[0]. " + " . \$entree[1] . " = " . strval(intval(\$entree[0])+intval(\$entree[1])) . "\n"', '$entree=explode("\r\n",$_POST["stdin"]); if($entree[0]==0){   $entree[0]=rand(0,999); } if($entree[1]==0){   $entree[1]=rand(0,999); } if($_POST[\'stdin\']==\'\') $_POST[\'stdin\']="$entree[0]\r\n$entree[1]";  ', '', '', 'print(42)', ''); 
