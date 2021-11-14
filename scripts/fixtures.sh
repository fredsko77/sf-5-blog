#!/bin/bash

if php bin/console doctrine:database:drop -f; then  
    echo "Suppression de la base de données... "
else 
    echo "La base de données n'a pas pu être supprimée ! "
fi 


if php bin/console doctrine:database:create; then  
    echo "Création de la base de données... "
else 
    echo "La base de données n'a pas pu être créée ! "
fi 

if [[ ! -d migrations/ ]]; then  
    echo "Création du dossier `migrations`... "
    mkdir -p migrations/
    echo "Création des fichiers de migrations... "
    php bin/console make:migration -n 
fi 

echo "Création de la structure de la base de données... "
php bin/console doctrine:migrations:migrate -n

echo "Chargement des fausses données... "
if php bin/console doctrine:fixtures:load -n; then  
    echo "Données chargées 🚀"
    echo "Nettoyage du cache..."
    php bin/console cache:clear -n
else 
    echo "Les jeu de données n'a pas pu être chargée dans la base !"
    exit 0
fi 

