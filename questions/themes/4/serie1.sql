INSERT INTO serie (themeID, serieID, numero, titre, description) VALUES (4, 16, 1, "Les expressions et les entrées/sorties", "Exercices de base sur les expressions et les opérations de saisie et d\'affichage");

    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 1, 16,'Affichage de base', 'Affichage de base', 'Faites afficher «Bonjour le monde!» suivi d\'un retour de chariot.');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), '"Bonjour le monde!\n"', '', '', '#include <iostream>\n\nusing namespace std;\n', '', ''); 

INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 2, 16,'Entrée d\'entiers Positif/Négatif', 'Entrée d\'entiers Positif/Négatif', 'Faites un programme qui reçoit un nombre entier en entrée, détermine s\'il est positif ou négatif et affiche la réponse : «Positif» ou «Négatif».');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), '($r<0?"Négatif":"Positif") . "\n"', '', '', '#include <iostream>\n\nusing namespace std;\n', '', ''); 
