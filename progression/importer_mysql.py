import MySQLdb
import urllib.parse


def importer(thème, uri):
    cnx = get_connexion(uri)
    c = cnx.cursor()
    c.execute(
        """INSERT INTO theme
                       (lang, 
                       titre, 
                       ordre, 
                       description) 
                  VALUES ( %s, %s, 0, %s )
           ON DUPLICATE KEY UPDATE
                       lang=%s,
                       titre=%s,
                       description=%s""",
        [
            thème["lang"],
            thème["titre"],
            thème["description"],
            thème["lang"],
            thème["titre"],
            thème["description"],
        ],
    )

    thème_id = c.execute("SELECT themeID FROM theme WHERE titre = %s", [thème["titre"]])
    for série in thème["séries"]:
        importer_série(c, thème_id, série)

    cnx.commit()
    cnx.close()


def importer_série(c, thème_id, série):
    c.execute(
        """
        INSERT INTO serie(
            themeID,
            numero,
            titre,
            description)
        VALUES (%s, %s, %s, %s)
        ON DUPLICATE KEY UPDATE
            numero=%s,
            description=%s
        """,
        [
            thème_id,
            série["numéro"],
            série["titre"],
            série["description"],
            série["numéro"],
            série["description"],
        ],
    )


def get_connexion(uri):
    uri = urllib.parse.urlparse(uri)
    cnx = MySQLdb.connect(
        host=uri.hostname,
        user=uri.username,
        passwd=uri.password,
        db="quiz",
    )

    return cnx
