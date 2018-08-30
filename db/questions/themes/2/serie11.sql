INSERT INTO serie (themeID, serieID, numero, titre, description)
VALUES (2, 11, 4, "La classe Dé", "Implémentation de la classe Dé avec doctests");

    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 1, 11,'Question 1', 'Question 1', 'Complétez les tests de la classe Dé');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), 'null', '', '', '"\nimport random\n\nclass  Dé:\n    \\"\\"\\"\n    Un dé à jouer d\'un nombre variable de faces.\n    \n    \\"\\"\\"\n    def __init__(self, faces):\n        \\"\\"\\"\n        Initialise le dé.\n\n        Paramètre:\n        - faces : entier, le nombre de faces du dé.\n\n        Exemples :"', '        >>> ', '"\n        >>> dé_test.face\n        1\n\n        \\"\\"\\"\n        assert faces > 1, \\"Le nombre de faces doit être > 1\\"\n\n        #Le nombre de faces du dé\n        self.__faces = faces\n\n        #Le nombre de points sur la face supérieure\n        self.__face = 1\n\n    @property\n    def face(self):\n        \\"\\"\\"\n        Propriété en lecture seule de la face supérieure du dé.\n\n        Retour : un entier représentant le nombre de points sur la face supérieure du dé.\n\n        \\"\\"\\"\n        return self.__face\n\n# --- Exécute les tests ---\nif __name__ == \\"__main__\\":\n    import doctest\n    doctest.testmod()\n"');
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 2, 11,'Question 2', 'Question 2', 'Complétez les tests de la classe Dé');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), 'null', '', '', '"\nimport random\n\nclass  Dé:\n    \\"\\"\\"\n    Un dé à jouer d\'un nombre variable de faces.\n    \n    \\"\\"\\"\n    def __init__(self, faces):\n        \\"\\"\\"\n        Initialise le dé.\n\n        Paramètre:\n        - faces : entier, le nombre de faces du dé. Doit être >1.\n\n        \\"\\"\\"\n        assert faces > 1, \\"Le nombre de faces doit être > 1\\"\n        self.__faces = faces\n        self.__face = 1\n            \n    def lancer(self):\n        \\"\\"\\"\n        Simule un lancer de dé\n        \n        retour: le nombre aléatoire sur le dé.\n        \n        Exemple:\n        >>> nb_faces = 6\n        >>> dé_test = Dé( nb_faces )"', '', '"\n        >>> résultat > 0 and résultat <= nb_faces\n        True\n\n        \\"\\"\\"\n        self.__face = random.randrange(self.__faces) + 1\n        return self.__face\n\n    @property\n    def face(self):\n        \\"\\"\\"\n        Propriété en lecture seule de la face supérieure du dé.\n\n        Retour : un entier représentant le nombre de points sur la face supérieure du dé.\n\n        \\"\\"\\"\n        return self.__face\n\n# --- Exécute les tests ---\nif __name__ == \\"__main__\\":\n    import doctest\n    doctest.testmod()\n"');
    
INSERT INTO question (type, numero, serieID, titre, description, enonce) VALUES (0, 3, 11,'Question 3', 'Question 3', 'Complétez les tests de la classe Dé');
INSERT INTO question_prog (questionID, reponse, setup, pre_exec, pre_code, in_code, post_code) VALUES ((SELECT max(questionID) FROM question), 'null', '', '', '"\nimport random\n\nclass  Dé:\n    \\"\\"\\"\n    Un dé à jouer d\'un nombre variable de faces.\n    \n    \\"\\"\\"\n    def __init__(self, faces):\n        \\"\\"\\"\n        Initialise le dé.\n\n        Paramètre:\n        - faces : entier, le nombre de faces du dé.\n\n        \\"\\"\\"\n        assert faces > 1, \\"Le nombre de faces doit être > 1\\"\n        self.__faces = faces\n        self.__face = 1\n            \n    def lancer(self):\n        \\"\\"\\"\n        Simule un lancer de dé\n        \n        retour: le nombre aléatoire sur le dé.\n        \n        Exemple:\n        >>> nb_faces = 6\n        >>> dé_test = Dé( nb_faces )\n        >>> résultat = dé_test.lancer()\n        >>> résultat > 0 and résultat <= nb_faces\n        True\n\n        \\"\\"\\"\n        self.__face = random.randrange(self.__faces) + 1\n        return self.__face\n\n    @property\n    def face(self):\n        \\"\\"\\"\n        Propriété en lecture seule de la face supérieure du dé.\n\n        Retour : un entier représentant le nombre de points sur la face supérieure du dé.\n\n        Exemples :\n        >>> dé_test = Dé(12)\n        >>> dé_test.face"', '', '"\n\n        \\"\\"\\"\n        return self.__face\n\n# --- Exécute les tests ---\nif __name__ == \\"__main__\\":\n    import doctest\n    doctest.testmod()\n"');