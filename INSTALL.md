Quiz python 1.0 requiert :
 * compilebox modifié (https://git.dept-info.crosemont.quebec/plafrance/compilebox)
 * docker
 * docker-compose v1.13+
 * Pour l'utilisation de LDAP sous Debian : php5-ldap

Configuration :
 * La configuration de l'application se fait dans le fichier quiz.conf
 * au besoin, utiliser l'exemple fournit dans quiz.conf.exemple
 * Pour une installation minimale, le type d'authentification peut être sélectionné à "no".

Compilation des images docker :
 * docker-compose build

Démarrage des conteneurs :
 * docker-compose up -d

L'application est accessible via :
 * https://localhost
 * utilisateur/mdp : admin/admin
 
Pour obtenir les questions système, dépendantes de conteneurs propres,
 * cd conteneurs_sys && ./build_all
