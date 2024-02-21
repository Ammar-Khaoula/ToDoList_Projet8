# Projet 8 ToDo List

Améliorez une application existante de ToDo & Co


## Environnement utilisé durant le développement

- Symfony 6.4
- Composer 2.5.8
- PHP: 8.1

## Installation

1.Clonez ou téléchargez le repository GitHub dans le dossier voulu :

```bash
git clone https://github.com/saro0h/projet8-TodoList.git

```
2.Editez le fichier situé à la racine intitulé .env.local qui devra être crée à la racine du projet en réalisant une copie du fichier .env afin de remplacer les valeurs de paramétrage de la base de données:

```bash
//Exemple : mysql://root:@127.0.0.1:3306/todolist_projet8"
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"

```
3.Installez les dépendances back-end du projet avec Composer:

```bash
composer install

```
4.Créez la base de données, taper la commande ci-dessous en vous plaçant dans le répertoire du projet:

```bash
symfony console doctrine:database:create

```
5.Créez les différentes tables de la base de données en appliquant les migrations:

```bash
symfony console doctrine:migrations:migrate

```
6.Après avoir créer votre base de données, vous pouvez également injecter un jeu de données en effectuant la commande suivante:

```bash
symfony console doctrine:fixtures:load

```
7.Tests:

a- Editez le fichier situé à la racine intitulé .env.test: 
```bash
//Exemple : mysql://root:@127.0.0.1:3306/todoTest"

```
b- Créer une base de données et des tables:

```bash
symfony console doctrine:database:create

symfony console doctrine:migrations:migrate

```

c- DataFixtures:

```bash
symfony console doctrine:fixtures:load

``` 

```bash
# Exécuter des tests
./vendor/bin/phpunit

```
```bash
# coverage de l’application
--coverage-html public/test-coverage

```

## Contribuant


Si vous souhaitez contribuer à ce projet, veuillez lire CONTRIBUTING.md
