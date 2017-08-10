/* Manipulation de répertoires */
INSERT INTO serie (themeID, serieID, numero, titre, description)
VALUES (3, 14, 1, "Manipulations de répertoires en bash", "Exercices de manipulation de répertoires grâce à l\'interpréteur de commande bash." );

/* Question 1 */
INSERT INTO question (type, serieID, description, numero, titre,  enonce)
VALUES (
1,
14,
"Création d'un répertoire",
1,
"Création d'un répertoire",
"Créez un répertoire nommé <em>burger</em> dans le répertoire personnel de <em>krusty</em>");
INSERT INTO question_systeme (questionID, image, user, verification, reponse)
VALUES (
(SELECT max(questionID) FROM question),
"qsystem",
"krusty",
"-d /home/krusty/burger",
null);
/* Question 2*/
INSERT INTO question (type, serieID, description, numero, titre,  enonce)
VALUES (
1,
14,
"Suppression d'un répertoire",
2,
"Suppression d'un répertoire",
"Supprimez le répertoire nommé <em>bart</em> dans le répertoire personnel de <em>krusty</em> ainsi que tout son contenu.");
INSERT INTO question_systeme (questionID, image, user, verification, reponse)
VALUES (
(SELECT max(questionID) FROM question),
"q2",
"krusty",
"! -d /home/krusty/bart",
null);
