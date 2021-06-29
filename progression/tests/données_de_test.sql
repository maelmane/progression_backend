DELETE FROM reponse_prog;
DELETE FROM avancement;
DELETE FROM sauvegarde;
DELETE FROM user;

INSERT INTO user VALUES (
  "bob",
  "motDePasse",
  1,
  0
);
INSERT INTO user VALUES (
  "admin",
  "mdpAdmin",
  1,
  1
);
INSERT INTO user VALUES (
  "Stefany",
  NULL,
  1,
  0
);

INSERT INTO cle VALUES (
  "bob",
  "1234",
  1624593600,
  1624680000,
  1
);

INSERT INTO sauvegarde VALUES (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1620150294,
  "python",
  "print(\"Hello world!\")"
);
INSERT INTO sauvegarde VALUES (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1620150375,
  "java",
  "System.out.println(\"Hello world!\");"
);

INSERT INTO avancement VALUES (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  0,
  3
);

INSERT INTO avancement VALUES (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
  1,
  3
);

INSERT INTO avancement VALUES (
  "Stefany",
  "https://exemple.com",
  1,
  3
);

INSERT INTO reponse_prog VALUES (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1615696276,
  "python",
  "print(\"Tourlou le monde!\")",
  0,
  2
);

INSERT INTO reponse_prog VALUES (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
  1615696286,
  "python",
  "print(\"Allo le monde!\")",
  0,
  3
);

INSERT INTO reponse_prog VALUES (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
  1615696296,
  "python",
  "print(\"Allo tout le monde!\")",
  1,
  4
);
