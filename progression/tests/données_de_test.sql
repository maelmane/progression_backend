DELETE FROM commentaire;
DELETE FROM reponse_prog;
DELETE FROM avancement;
DELETE FROM sauvegarde;
DELETE FROM cle;
DELETE FROM user;

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
  "Bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  0,
  "Un titre",
  "facile",
  1645739981,
  1645739959 
);

INSERT INTO avancement VALUES (
  "Bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
  1,
  "Un titre",
  "facile",
  1645739981,
  1645739959
);

INSERT INTO avancement VALUES (
  "Bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction2",
  1,
  "Un titre 2",
  "facile",
  1645739991,
  1645739969
);

INSERT INTO avancement VALUES (
  "Stefany",
  "https://exemple.com",
  1,
  "Bob",
  "facile",
  1645739981,
  1645739959
);

INSERT INTO avancement VALUES (
  "jdoe",
  "https://exemple2.com",
  1,
  "Question Système",
  "facile",
  1645739981,
  1645739959
);

INSERT INTO avancement VALUES (
  "jdoe",
  "https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions",
  1,
  "Toutes Permissions",
  "facile",
  1645739981,
  1645739959
);

INSERT INTO reponse_prog VALUES (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1615696276,
  "python",
  "print(\"Tourlou le monde!\")",
  0,
  2,
  3456
);

INSERT INTO reponse_prog VALUES (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
  1615696286,
  "python",
  "print(\"Allo le monde!\")",
  0,
  3,
  34567
);

INSERT INTO reponse_prog VALUES (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
  1615696296,
  "python",
  "print(\"Allo tout le monde!\")",
  1,
  4,
  345633
);

INSERT INTO reponse_sys VALUES (
  "jdoe",
  "https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions",
  "leConteneur",
  "laRéponse",
  1615696300,
  0,
  0,
  0
);

INSERT INTO reponse_sys VALUES (
  "jdoe",
  "https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions",
  "leConteneur2",
  "laRéponse2",
  1615696301,
  1,
  1,
  0
);

INSERT INTO commentaire VALUES(
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1615696276,
  1,
  "le 1er message",
  "jdoe",
  1615696276,
  14
);

INSERT INTO commentaire VALUES(
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1615696276,
  2,
  "le 2er message",
  "admin",
  1615696276,
  12
);

INSERT INTO commentaire VALUES(
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1615696276,
  3,
  "le 3er message",
  "Stefany",
  1615696276,
  14
);
