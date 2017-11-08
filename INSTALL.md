Quiz python 1.0 requiert :
 * compilebox modifié (https://git.dept-info.crosemont.quebec/plafrance/compilebox)
 * docker

Compilation de l'image docker :
 * docker build -t quiz .

Démarrage du conteneur docker :
 * docker run -d --name quiz -p 443:443 quiz

L'application est accessible via :
 * https://localhost
 * utilisateur/mdp : admin/admin
 