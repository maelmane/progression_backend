#!/bin/sh

# Lance les migrations BD
/var/www/progression/db/build_db.sh && \

# Lance la validation  des variables d'environnement
php progression/app/validateurEnv.php && \

# Lance le serveur
apache2-foreground
