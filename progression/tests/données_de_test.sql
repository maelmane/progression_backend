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
), (
  "bob",
  "clé de test 2",
  "2345",
  1624593602,
  1624680002,
  1,
  2
);

INSERT INTO avancement(id, type, username, question_uri, etat, titre, niveau, date_modification, date_reussite, user_id) VALUES (
  1,
  "prog",
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1,
  "Un titre",
  "facile",
  1615696276,
  null,
  2
),(
  2,
  "prog",
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
  1,
  "Un titre",
  "facile",
  1645739981,
  1645739959,
  2
),(
  3,
  "prog",
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction2",
  2,
  "Un titre 2",
  "facile",
  1645739991,
  1645739969,
  2
),(
  4,
  "prog",
  "Stefany",
  "https://exemple.com",
  1,
  "Bob",
  "facile",
  1645739981,
  1645739959,
  4
),(
  5,
  "sys",
  "jdoe",
  "https://exemple2.com",
  1,
  "Question Système",
  "facile",
  1645739981,
  1645739959,
  1
),(
  6,
  "sys",
  "jdoe",
  "https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions",
  1,
  "Toutes Permissions",
  "facile",
  1645739981,
  1645739959,
  1
);

INSERT INTO sauvegarde(
  `username`,
  `question_uri`,
  `date_sauvegarde`,
  `langage`,
  `code`,
  `avancement_id`
) VALUES (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1620150294,
  "python",
  "print(\"Hello world!\")",
  1
), (
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1620150375,
  "java",
  "System.out.println(\"Hello world!\");",
  1
);

INSERT INTO reponse_prog(id, username, question_uri, date_soumission, langage, code, reussi, tests_reussis, temps_exécution, avancement_id) VALUES (
  1,
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction",
  1615696276,
  "python",
  "print(\"Tourlou le monde!\")",
  0,
  2,
  3456,
  1
),(
  2,
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
  1615696286,
  "python",
  "print(\"Allo le monde!\")",
  0,
  3,
  34567,
  3
),(
  3,
  "bob",
  "https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction",
  1615696296,
  "python",
  "print(\"Allo tout le monde!\")",
  1,
  4,
  345633,
  3
);

INSERT INTO reponse_sys(username, question_uri, conteneur, reponse, date_soumission, reussi, tests_reussis, temps_exécution) VALUES (
  "jdoe",
  "https://depot.com/roger/questions_sys/permissions01/octroyer_toutes_les_permissions",
  "leConteneur",
  "laRéponse",
  1615696300,
  0,
  0,
  0
),(
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
