use quiz;

insert into theme values (null, 0, "python_intro", "Python : introduction", "Ces exercices portent sur les concepts de base de la programmation structur_e, en utilisant le langage Python");
insert into theme values (null, 7, "c_intro", "C : introduction", "Ces exercices portent sur les concepts de base du langage C");

insert into serie values(null, 1, 1, "entrees_sorties", "Les expressions et les entr_es/sorties", "3a2502b6a2", "Les expressions et les entr_es/sorties");

insert into question (serieID, description, numero, nom, titre, lang, setup, enonce, pre_exec, pre_code, code, post_code, reponse, params, stdin, points)
values(
1,
"Affichage avec variables",
2,
"affichage_variables",
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
);
