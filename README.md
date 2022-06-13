# Projet en cours de développement
## Site-depense

## Choix techniques :
PHP et MySQL
Symfony 


## Pré-requis

PHP >=8.0.2
mySQL ou MariaDB

NodeJS

Composer

NPM ou Yarn


### Lancer le projet
Cloner ce repository
> git clone git@github.com:ExTerros/Site-depense.git

Installer les dépendances via Composer et NPM ou YARN
> composer install
> npm install


Compiler le CSS
> npm run dev

Créer un fichier .env.local et coller en modifiant ce qui est nécessaire
> DATABASE_URL="mysql://root:@127.0.0.1:3306/depense?serverVersion=5.7&charset=utf8mb4"

Créer la base de données
> composer prepare
