/* Introduction à VIM */
INSERT INTO serie (themeID, serieID, numero, titre, description)
VALUES (3, 15, 2, "Introduction à VIM", "Exercices d'introduction à l'éditeur de texte VIM." );

/* Question 1*/
INSERT INTO question (type, serieID, numero, description, titre,  enonce)
VALUES (
1,
15,
1,
"Quitter VIM",
"Quitter VIM",
"Ho non! Vous avez démarré VIM par accident. Sortez-en!!!");
INSERT INTO question_systeme (questionID, image, user, verification, reponse)
VALUES (
(SELECT max(questionID) FROM question),
"q2-1",
"krusty",
'! -f /tmp/canari',
null);

/* Question 3*/
INSERT INTO question (type, serieID, numero, description, titre,  enonce)
VALUES (
1,
15,
2,
"VIM édition 1",
"VIM édition 1",
"Vous devez modifier le fichier nommé <em>réponse</em> avec le seul éditeur de texte disponible: VIM.");
INSERT INTO question_systeme (questionID, image, user, verification, reponse)
VALUES (
(SELECT max(questionID) FROM question),
"q2-2",
"krusty",
'"$(grep "complété = oui" -c /home/krusty/réponse)" == "1" && "$(ls -s /home/krusty/réponse|cut -d " " -f 1)" == "12"',
null);
