#!/bin/bash

php bin/console doctrine:database:create -n
if [ $? -eq 0 ]
then
    echo "it worked"
else
    echo "it failed"
fi

if [[ -d migrations/ ]]; then  
    echo "Mise à jour de la structure de la base de données..."
    php bin/console doctrine:migrations:migrate -n
else
    echo "Création du dossier `migrations`..."
    mkdir -p migrations/
    echo "Création des fichiers de migrations..."
    php bin/console make:migration -n 
    echo "Création de la structure de la base de données..."
    php bin/console doctrine:migrations:migrate -n
fi 

# symfony server:start --port=4000 -d

# start http://localhost:4000