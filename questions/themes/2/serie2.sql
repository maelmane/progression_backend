/* Question 1 */
INSERT INTO question (questionID, type, serieID, description, numero, titre,  enonce)
VALUES (
3,
1,
2,
"Création d'un répertoire",
1,
"Création d'un répertoire",
"Créez un répertoire nommé <em>burger</em> dans le répertoire personnel de <em>krusty</em>");
INSERT INTO question_systeme (questionID, image, user, verification, reponse)
VALUES (
3,
"qsystem",
"krusty",
"-d /home/krusty/burger",
null);
/* Question 2*/
INSERT INTO question (questionID, type, serieID, description, numero, titre,  enonce)
VALUES (
4,
1,
2,
"Suppression d'un répertoire",
2,
"Suppression d'un répertoire",
"Supprimez le répertoire nommé <em>bart</em> dans le répertoire personnel de <em>krusty</em> ainsi que tout son contenu.");
INSERT INTO question_systeme (questionID, image, user, verification, reponse)
VALUES (
4,
"q2",
"krusty",
"! -d /home/krusty/bart",
null);
