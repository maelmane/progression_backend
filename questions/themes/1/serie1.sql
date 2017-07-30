/* Question 1 */
INSERT INTO question (questionID, type, serieID, description, numero, titre,  enonce)
VALUES (
1,
0,
1,
"Affichage avec variables",
1,
"Affichage avec variables",
"Faites afficher la somme des nombres alpha et beta.");
INSERT INTO question_prog (questionID, setup, pre_exec, pre_code, incode, post_code, reponse)
VALUES (
1,
"$r=rand(0,1000);
$s=rand(0,1000);
$somme=$r+$s;
",
"",
"alpha=$r\nbeta=$s",
"print(42)",
"",
"$somme"
);
/* Question 2 */
INSERT INTO question (questionID, type, serieID, description, numero, titre,  enonce)
VALUES (
2,
0,
1,
"Bonjour le monde!",
2,
"Bonjour le monde!",
"Faites afficher «Bonjour le monde!».");
INSERT INTO question_prog (questionID, setup, pre_exec, pre_code, incode, post_code, reponse)
VALUES (
2,
"$r=rand(0,1000);",
"",
"",
"print(42)",
"",
"Bonjour le monde!"
);
