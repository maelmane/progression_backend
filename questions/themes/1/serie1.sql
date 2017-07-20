use quiz

insert into question (questionID, serieID, description, numero, titre, lang, setup, enonce, pre_exec, pre_code, incode, post_code, reponse, params, stdin, points)
values
/* Question 1 */
(
1,
1,
"Affichage avec variables",
2,
"Affichage avec variables",
0,
"$r=rand(0,1000);
$s=rand(0,1000);
$somme=$r+$s;
",
"Faites afficher la somme des nombres alpha et beta.",
"",
"alpha=$r\nbeta=$s",
"print(42)",
"",
"$somme",
"",
"",
0
),
/* Question 2 */
(
2,
1,
"Bonjour le monde!",
1,
"Bonjour le monde!",
0,
"$r=rand(0,1000);",
"Faites afficher «Bonjour le monde!».",
"",
"",
"print(42)",
"",
"Bonjour le monde!",
"",
"",
0);
