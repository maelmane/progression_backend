DELETE FROM reponse_prog;
DELETE FROM avancement;
DELETE FROM sauvegarde;
DELETE FROM cle;
DELETE FROM user;
DELETE FROM commentaire;

INSERT INTO user VALUES (
  "jdoe",
  "Crosemont2021!",
  1,
  0
), (
  "bob",
  "motDePasse",
  1,
  0
), (
  "admin",
  "mdpAdmin",
  1,
  1
), (
  "Stefany",
  NULL,
  1,
  0
);

INSERT INTO cle VALUES (
  "bob",
  "clé de test",
  "1234",
  1624593600,
  1624680000,
  1
),
(
  "bob",
  "clé de test 2",
  "2345",
  1624593602,
  1624680002,
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

INSERT INTO commentaire VALUES(
  1,
  "le 1er message",
  "Jean",
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1615696276,
  "jdoe"
);

INSERT INTO commentaire VALUES(
  2,
  "le 2er message",
  "Pat",
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1615696276,
  "jdoe"
);

INSERT INTO commentaire VALUES(
  3,
  "le 3er message",
  "bob12",
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1615696276,
  "jdoe"
);
