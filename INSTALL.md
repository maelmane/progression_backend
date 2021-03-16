# Progression backend

## 1. Dépendances obligatoires
- [git](https://git-scm.com/downloads)
- [docker](https://www.docker.com/)
- [compilebox (modifié)](https://git.dti.crosemont.quebec/progression/compilebox)

## 2. Installation & Configuration 
### 2.1 Obtenir le code source
- Cloner le projet
```
git clone https://git.dti.crosemont.quebec/progression/progression_backend.git
```

Les commandes suivantes doivent être faites à partir du répertoire `progression_backend/progression`
```
cd progression_backend/progression
```

### 2.2 Créer et adapter le fichier de configuration
- Copier le ficher **.env** dans le répertoire **/progression/app**
```
cp .env.exemple .env
```
- Modifier le type d\'authentification et l\'hôte pour le compilebox du fichier **.env** 

#### En développement :
- Désactiver l'authentification
```
AUTH_TYPE = "no"
```
- Effectuer les compilations sur la machine de développement
```
COMPILEBOX_HOTE = 172.20.0.1
```

### 2.3 Construire les images docker
- Compilation des images docker
```
docker-compose build
```

### 2.4 Exécuter les tests (facultatif)
```
docker-compose up tests
```

### 2.5 Initialiser la base de données
- Création (ou réinitialisation) de la base de données
```
docker-compose up -d db
```
(laissez quelques secondes de démarrage au SGBD)
```
docker exec -it progression_db bash
cd /tmp/ && ./build_db.sh
```
Fermer le terminal avec Ctrl-D ou `exit`

### 2.6 Importer des exercices (facultatif)

- Spécifier la source des exercices et la BD de destination via les variables SOURCE et DESTINATION dans docker-compose.yml (les valeurs par défaut fournissent les questions démos)
- Effectuer l'importation :
```
docker-compose up importeur
```

### 2.7 Obtenir les questions système, dépendantes de conteneurs propres (facultatif)
- Construire les conteneurs :
```
cd conteneurs && ./build_all
```

### 2.8 Démarrer l'application
- Démarrage des conteneurs progression et progression_db
```
docker-compose up -d progression
```
- Pour voir ce qui est en cours d\'exécution
```
docker-compose ps
```
Les conteneurs `progression`et `progression_db` devraient être «Up»

L\'application est accessible via:
- http://172.20.0.3/

Sans authentification, les utilisateurs sont automatiquement créés dès leur connexion sans mot de passe.

## 3. FAQ
Q: Pourquoi `docker-compose build` me donne des erreurs ?
- Assurez-vous que votre utilisateur fait partie du groupe docker. Le résultat de la commande `groups` devrait inclure le groupe `docker`.

- Assurez-vous que Docker est en marche!
```
systemctl enable docker
systemctl start docker
```

