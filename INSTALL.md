# Progression backend
Voici la procédure d'installation pour le backend de Progression. 

## 1. Installation & Configuration 

### Dépendances obligatoires
- [git](https://git-scm.com/downloads)
- [docker](https://www.docker.com/)
- [compilebox (modifié)](https://git.dti.crosemont.quebec/progression/compilebox)

### Obtenir le code source
Cloner le projet **progression_backend**
```
git clone https://git.dti.crosemont.quebec/progression/progression_backend.git (HTTPS)
git clone git@git.dti.crosemont.quebec:progression/progression_backend.git (SSH)
```

### Créer et adapter le fichier de configuration
Créer le fichier .env ou copier le ficher d'exemple `.env.exemple` du répertoire `/progression/app`
```
cp app/.env.exemple app/.env
```
Modifier le type d'authentification et l'hôte pour le compilebox du fichier `.env`

### En développement
Désactiver **l'authentification** et effectuer les compilations avec l'exécuteur **compilebox** localement.
```
AUTH_TYPE=no
COMPILEBOX_HOTE=172.20.0.1
```
Sans authentification, les utilisateurs sont automatiquement créés dès leur connexion sans mot de passe.

### Construire les images docker
Compilation des images docker
```
docker-compose build progression
```

### Initialiser la base de données
Création (ou réinitialisation) de la base de données
```
docker-compose up -d db
```
(laissez quelques secondes de démarrage au SGBD)
```
docker exec -it progression_db bash
cd /tmp/ && ./build_db.sh
```
Fermer le terminal avec Ctrl-D ou `exit`

## 2. Démarrer l'application
Démarrage des conteneurs `progression` et `progression_db`
```
docker-compose up -d progression
```
Pour voir ce qui est en cours d'exécution
```
docker-compose ps
```
L'application est accessible via: http://172.20.0.3/

## 3. Exécution des tests (facultatif)
Copier le ficher `.env.exemple` du répertoire `/progression/tests`
```
cp app/.env tests/.env
```

Lancer les tests
```
docker-compose up tests
```

## 4. FAQ
Q: Pourquoi `docker-compose build` me donne des erreurs ?
- Assurez-vous que votre utilisateur fait partie du groupe docker. Le résultat de la commande `groups` devrait inclure le groupe `docker`.

- Assurez-vous que Docker est en marche!
```
systemctl enable docker
systemctl start docker
```

Q: Comment supprimer les images et les conteneurs inutiles ?
- Utiliser ce script :
```
docker images --no-trunc | grep '<none>' | awk '{ print $3 }' | xargs -r docker rmi                                                                
docker ps --filter status=dead --filter status=exited -aq | xargs -r docker rm
docker volume ls -qf dangling=true | xargs -r docker volume rm
```

