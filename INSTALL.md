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
AUTH_LOCAL=no
AUTH_LDAP=no
COMPILEBOX_HOTE=172.20.0.1
```

Sans authentification, les utilisateurs sont automatiquement créés dès leur connexion sans mot de passe.

### Construire les images docker

Compilation des images docker

```
docker-compose build progression
```

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

Lancer les tests

```
docker-compose run tests
```
