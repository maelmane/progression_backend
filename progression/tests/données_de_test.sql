DELETE FROM commentaire;
DELETE FROM reponse_prog;
DELETE FROM avancement;
DELETE FROM sauvegarde;
DELETE FROM cle;
DELETE FROM user;

INSERT INTO user(id, username, password, actif, role) VALUES (
  1,
  "jdoe",
  "Crosemont2021!",
  1,
  0
), (
  2,
  "bob",
  "motDePasse",
  1,
  0
), (
  3,
  "admin",
  "mdpAdmin",
  1,
  1
), (
  4,
  "Stefany",
  NULL,
  1,
  0
);

INSERT INTO cle(username, nom, hash, creation, expiration, portee, user_id) VALUES (
  "bob",
  "clé de test",
  "1234",
  1624593600,
  1624680000,
  1,
  2
),
(
  "bob",
  "clé de test 2",
  "2345",
  1624593602,
  1624680002,
  1,
  2
);

INSERT INTO sauvegarde(
  `username`,
  `question_uri`,
  `date_sauvegarde`,
  `langage`,
  `code`
  ) VALUES (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1620150294,
  "python",
  "print(\"Hello world!\")"
  ), (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1620150375,
  "java",
  "System.out.println(\"Hello world!\");"
);

INSERT INTO avancement(username, question_uri, etat, type, titre, niveau, date_modification, date_reussite, user_id) VALUES (
  "bob",/*USER*/
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  /**/
  0,/**/
  3,/*ÉTAT*/
  "Bob",/*TITRE*/
  "facile",/*NIVEAU*/
  1645739981,/*DATE*/
  1645739959 /*DATE*/,
  2
), (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
  1,
  3,
  "Bob",
  "facile",
  1645739981,
  1645739959,
  2
), (
  "Stefany",
  "https://exemple.com",
  1,
  3,
  "Bob",
  "facile",
  1645739981,
  1645739959,
  4
);

INSERT INTO reponse_prog( username, question_uri, date_soumission, langage, code, reussi, tests_reussis, avancement_id) VALUES (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1615696276,
  "python",
  "print(\"Tourlou le monde!\")",
  0,
  2,
  1
), (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
  1615696286,
  "python",
  "print(\"Allo le monde!\")",
  0,
  3,
  2
), (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
  1615696296,
  "python",
  "print(\"Allo tout le monde!\")",
  1,
  4,
  2
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
