# MyGiftBox
Membres du projet :
- Maeva Butaye    ( Lilychaan )
- Camille Schwarz ( S-Camille )
- Léo Galassi     ( ElGitariste )
- Quentin Rimet   ( QuentinRimet )

Lien pour consulter le tableau de bord de ce projet : https://trello.com/b/XFk4JLFC/mygiftboxapp

Lien pour consulter l'application : https://webetu.iutnc.univ-lorraine.fr/www/schwarz16u/MyGiftBox/

# Pour tester l'application sur webetu

* Des comptes membre sont déjà créés :
    - Pour un compte non administrateur, il suffit de saisir "visiteur@visiteur.fr" en tant que mail et "visiteur" en tant que mot de passe lors de la connection
    - Pour un compte administrateur, il suffit de saisir par exemple "camille@camille.fr" en tant que mail et "camille" en tant que mot de passe lors de la connection

* Si vous souhaitez modifier ou consulter la base de données : il suffit d'aller sur https://webetu.iutnc.univ-lorraine.fr/phpmya/ , de saisir "schwarz16u" en tant qu'utilisateur et "mysql" en tant que mot de passe.

# Installation via Docker :

Prérequis : 

* Docker
* Docker Compose
* Clone du dépôt soit :
    - via SSH : git clone git@github.com:Les-Poros/MyGiftBox.git
    - via HTTPS : git clone https://github.com/Les-Poros/MyGiftBox.git

Pour installer et lancer en production :
```
  docker-compose -f ./docker-compose.yml build
  docker-compose -f ./docker-compose.yml up -d
```
Un makefile est aussi mit à disposition afin d'aider : lancer "make help" pour connaitre les commandes utilisables.

Ouvrez alors un navigateur sur http://localhost:8080 pour lancer le projet.

Attention : Le chargement de la bdd peut prendre un certain temps et faire apparaitre une erreur pdo si l'on charge la page web avant la fin de ce chargement, il suffit juste d'attendre un peu avant de recharger la page.

Il y a aussi un phpmyadmin qui est accessible sur http://localhost:8081. Pour s'y connecter, il faut mettre root en utilisateur et aucun mot de passe.
