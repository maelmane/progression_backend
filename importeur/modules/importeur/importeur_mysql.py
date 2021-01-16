import MySQLdb
import urllib.parse

langages={
    "python2" : 0,
    "python" : 1,
    "ruby" : 2,
    "clojure" : 3,
    "php" : 4,
    "js" : 5,
    "scala" : 6,
    "go" : 7,
    "cpp" : 8,
    "c" : 9,
    "java" : 10,
    "bash" : 11,
    "perl" : 12,
}

def importer(thème, uri):
    cnx = get_connexion(uri)
    c = cnx.cursor()
    c.execute(
        """INSERT INTO theme
                       (nom,
                       lang, 
                       titre, 
                       ordre, 
                       description) 
                  VALUES ( %s, %s, %s, 0, %s )
           ON DUPLICATE KEY UPDATE
                       lang=%s,
                       titre=%s,
                       description=%s""",
        [
            thème["nom"],
            thème["lang"],
            thème["titre"],
            thème["description"],
            thème["lang"],
            thème["titre"],
            thème["description"],
        ],
    )

    c.execute("SELECT themeID FROM theme WHERE nom = %s", [thème["nom"]])
    thème_id = c.fetchone()
    for série in thème["séries"]:
        importer_série(c, thème_id, série)

    cnx.commit()
    cnx.close()


def importer_série(c, thème_id, série):
    c.execute(
        """
        INSERT INTO serie(
            nom,
            themeID,
            numero,
            titre,
            description)
        VALUES (%s, %s, %s, %s, %s)
        ON DUPLICATE KEY UPDATE
            numero=%s,
            titre=%s,
            description=%s
        """,
        [
            série["nom"],
            thème_id,
            série["numéro"],
            série["titre"],
            série["description"],
            série["numéro"],
            série["titre"],
            série["description"],
        ],
    )


    c.execute("SELECT serieID FROM serie WHERE themeID = %s AND nom = %s", [thème_id, série["nom"]])
    série_id = c.fetchone()
    for question in série["questions"]:
        importer_question(c, série_id, question)
    
def importer_question(c, série_id, question):
    c.execute(
        """
        INSERT INTO question(
            serieID,
            nom,
            type,
            titre,
            description,
            numero,
            enonce,
            feedback_pos,
            feedback_neg
        )
        VALUES ( %s, %s, 3, %s, %s, %s, %s, %s, %s)
        ON DUPLICATE KEY UPDATE
            titre=%s,
            description=%s,
            numero=%s,
            enonce=%s,
            feedback_pos=%s,
            feedback_neg=%s
        """,
        [
         série_id,
         question["nom"],
         question["titre"],
         question["description"],
         question["numéro"],
         question["énoncé"],
         question["feedback_pos"],
         question["feedback_neg"],
         question["titre"],
         question["description"],
         question["numéro"],
         question["énoncé"],
         question["feedback_pos"],
         question["feedback_neg"],
        ],
        )

    c.execute("SELECT questionID FROM question WHERE serieID= %s AND nom = %s", [série_id, question["nom"]])
    question_id = c.fetchone()
    for exécutable in question["exécutables"]:
        importer_exécutable(c, question_id, exécutable)
    for test in question["tests"]:
        importer_test(c, question_id, test)

def importer_exécutable(c, question_id, exécutable):
    c.execute(
        """
        INSERT INTO executable(
           questionID,
           code,
           lang)
        VALUES( %s, %s, %s)
        ON DUPLICATE KEY UPDATE
           code=%s
        """
        ,
        [
            question_id,
            exécutable["code"],
            langages[exécutable["langage"]],
            exécutable["code"],
            
        ],
        )
def importer_test(c, question_id, test):
    c.execute(
        """
        INSERT INTO test(
            questionID,
            nom,
            stdin,
            params,
            solution,
            feedback_pos,
            feedback_neg
        )
        VALUES( %s, %s, %s, %s, %s, %s, %s)
        ON DUPLICATE KEY UPDATE
            stdin=%s,
            params=%s,
            solution=%s,
            feedback_pos=%s,
            feedback_neg=%s
        """
        ,
        [
            question_id,
            test["nom"],
            test["in"],
            test["params"],
            test["out"],
            test["feedback_pos"],
            test["feedback_neg"],
            test["in"],
            test["params"],
            test["out"],
            test["feedback_pos"],
            test["feedback_neg"],
        ],
        )
        
def get_connexion(uri):
    uri = urllib.parse.urlparse(uri)
    cnx = MySQLdb.connect(
        host=uri.hostname,
        user=uri.username,
        passwd=uri.password,
        charset='utf8',
        db="quiz",
    )

    return cnx
