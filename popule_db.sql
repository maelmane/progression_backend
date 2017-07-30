/* Thèmes */
INSERT INTO theme (themeID, lang, ordre, titre, description)
VALUES (1, 0, 0, "Python : introduction", "Ces exercices portent sur les concepts de base de la programmation structurée, en utilisant le langage Python");
INSERT INTO theme (themeID, lang, ordre, titre, description)
VALUES (2, 0, 2, "Système d\'exploitation (Linux)", "Ces exercices portent sur l\'utilisation du système d\'exploitation Linux");

INSERT INTO theme (themeID, lang, ordre, titre, description)
VALUES (3, 7, 1, "C : introduction", "Ces exercices portent sur les concepts de base du langage C");

/* Python intro */
INSERT INTO serie (themeID, serieID, numero, titre, description)
VALUES (1, 1, 1, "Les expressions et les entrées/sorties", "Exercices de base sur les expressions et les opérations de saisie et d\'affichage");

/* Linux */
INSERT INTO serie (themeID, serieID, numero, titre, description)
VALUES (2, 2, 1, "Manipulations de fichier en bash", "Exercices de manipulation de fichiers grâce à l\'interpréteur de commande bash." );
