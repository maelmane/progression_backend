INSERT INTO serie (themeID, serieID, numero, titre, description)
VALUES (1, 4, 4, "Les chaînes de caractères", "Ces questions vous permettront de vérifier vos connaissances sur les chaînes de caractères.");

    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 1, 4,'Question 1', 'Question 1', 'Faites afficher la phrase «Lorem ipsum dolor sit amet, consectetur adipiscing elit.»');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), '"Lorem ipsum dolor sit amet, consectetur adipiscing elit."', '', '', '', '', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 2, 4,'Question 2', 'Question 2', 'Faites afficher la phrase donnée en majuscules.');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), 'strtoupper(\$phrase)', '$phrase=\'suspendisse eleifend tempor mi non vestibulum sem viverra in mauris placerat facilisis faucibus sed gravida tempus malesuada.\';  ', '', '\"phrase=\\"\$phrase\\"\"', '', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 3, 4,'Question 3', 'Question 3', 'Faites afficher le premier et le dernier caractère de la phrase donnée.');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), '\$phrase[0] . substr(\$phrase,-1)', '$lorem=explode(" ",\'suspendisse eleifend tempor mi non vestibulum sem viverra in mauris placerat facilisis faucibus sed gravida tempus malesuada.\'); $mots=array_slice($lorem, rand(0,5), rand(7,12)); $phrase=implode(" ",$mots);  ', '', '\"phrase=\\"\$phrase\\"\"', '', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 4, 4,'Question 4', 'Question 4', 'Faites afficher le premier et le dernier caractère de la phrase donnée.');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), '\$phrase[0] . substr(\$phrase,-1)', '$lorem=explode(" ",\'suspendisse eleifend tempor mi non vestibulum sem viverra in mauris placerat facilisis faucibus sed gravida tempus malesuada.\'); $mots=array_slice($lorem, rand(0,5), rand(7,12)); $phrase=implode(" ",$mots);  ', '', '\"phrase=\\"\$phrase\\"\"', '', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 5, 4,'Question 5', 'Question 5', 'Faites afficher la phrase donnée avec le premier caractère en majuscules.');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), 'ucfirst(\$phrase)', '$lorem=explode(" ",\'suspendisse eleifend tempor mi non vestibulum sem viverra in mauris placerat facilisis faucibus sed gravida tempus malesuada.\'); $phrase=implode(" ",array_slice($lorem, rand(0,5), rand(7,12)));  ', '', '\"phrase=\\"\$phrase\\"\"', '', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 6, 4,'Question 6', 'Question 6', 'Faites afficher le nombre de mots dans la phrase donnée.');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), 'sizeof(\$mots)', '$lorem=explode(" ",\'suspendisse eleifend tempor mi non vestibulum sem viverra in mauris placerat facilisis faucibus sed gravida tempus malesuada.\'); $mots=array_slice($lorem, rand(0,5), rand(7,12)); $phrase=implode(" ",$mots);  ', '', '\"phrase=\\"\$phrase\\"\"', '', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 7, 4,'Question 7', 'Question 7', 'Faites afficher la phrase donnée à l\'envers.');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), 'strrev(\$phrase)', '$lorem=explode(" ",\'suspendisse eleifend tempor mi non vestibulum sem viverra in mauris placerat facilisis faucibus sed gravida tempus malesuada.\'); $mots=array_slice($lorem, rand(0,5), rand(7,12)); $phrase=implode(" ",$mots);  ', '', '\"phrase=\\"\$phrase\\"\"', '', ''); 
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 8, 4,'Question 8', 'Question 8', 'Faites afficher les mots de la phrase donnée dans l\'ordre inverse.');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), '\$phrase', '$lorem=explode(" ",\'suspendisse eleifend tempor mi non vestibulum sem viverra in mauris placerat facilisis faucibus sed gravida tempus malesuada.\'); $mots=array_slice($lorem, rand(0,5), rand(7,12)); $phrase=implode(" ",$mots); $reponse=implode(" ",array_reverse($mots));  ', '', '\"phrase=\\"\$phrase\\"\"', '', ''); 