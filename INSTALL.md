Progression requiert :
 * compilebox modifié (https://git.dept-info.crosemont.quebec/progression/compilebox)
 * docker
 * docker-compose v1.13+

Configuration :
 * La configuration de l'application se fait dans le fichier quiz.conf
 * au besoin, utiliser l'exemple fournit dans quiz.conf.exemple
 * Pour une installation minimale, le type d'authentification peut être sélectionné à "no".

Compilation des images docker :
 * `docker-compose build` (l'avertissement «Do not run Composer as root/super user! » est normal)

Démarrage des conteneurs :
 * `docker-compose up -d progression`

Création (ou réinitialisation) de la base de données :
 * `docker exec -it progression_db bash`
 * `cd /tmp/ && ./build_db.sh`
 * Ctrl-D

Importation des questions/exercices de programmation :
 * Ajuster au besoin les variables d'environnement $SOURCE et $DESTINATION dans docker-compose.yml
 * `docker-compose up importeur`
  
L'application est accessible via :
 * https://172.20.0.3
  
Pour obtenir les questions système, dépendantes de conteneurs propres,
 * `cd progression/conteneurs_sys && ./build_all`
