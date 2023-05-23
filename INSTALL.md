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
git clone https://git.dti.crosemont.quebec/progression/progression_backend.git
cd progression_backend
```

### Créer et adapter le fichier de configuration

Créer le fichier .env ou copier le ficher d'exemple `env.exemple`

```
cp env.exemple .env
```

Modifier les options de configuration minimales :

#### URL d'origine
URL d'origine permise pour les requêtes à l'API. Devrait être l'URL de l'application web.

```
HTTP_ORIGIN=<URL de l'application web>
```

Exemple:
```
# URL d'origine permise pour les requêtes à l'API. Devrait être l'URL de l'application web.
HTTP_ORIGIN=https://progression.crosemont.qc.ca/
```

#### URL de l'API
URL de base de l'API

```
APP_URL=<URL de l'API>
```

Exemple:
```
# URL de base de l'API
APP_URL=https://progression.crosemont.qc.ca/api/v1
```

#### Secret JWT
Secret pour le chiffrement de token JWT. 
**GARDER ABSOLUMENT PRIVÉ.**

```
JWT_SECRET=<chaîne de caractères aléatoire>
```

Exemple:
```
# Secret JWT, À CHANGER ET GARDER PRIVÉ
JWT_SECRET=OGlW&]K-J}hpW@9b(SuJ
```

#### Type d'authentification
Type d'authentification requise

`AUTH_LOCAL` : permet l'inscription et l'authentification locale
`AUTH_LDAP` : permet l'authentification à partir d'un annuaire LDAP

Si aucune des deux formes d'authentification n'est exigée, l'inscription se fait sans mot de passe.

Exemple:
```
# Authentification locale permise ou via LDAP
AUTH_LOCAL=true
AUTH_LDAP=true
```

#### Exécuteur compilebox
L'URL de l'exécuteur Compilebox. Nécessaire pour effectuer les compilations et exécution de programmes.

Exemple:
```
# URL de l'exécuteur Compilebox
COMPILEBOX_URL=http://progression.dti.crosemont.quebec:12380/compile
```

### Démarrer l'application

Démarrage des conteneurs `api` et `db`

```
docker-compose up -d api
```

L'application est accessible via à l'adresse <APP_URL>

```
$ source .env
$ curl $APP_URL/
Progression 2.3.5(caef26)
```
