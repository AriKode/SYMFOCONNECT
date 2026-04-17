#!/bin/bash

# Script de déploiement SymfoConnect

echo "--- Déploiement en cours ---"

# 1. Installation des dépendances
composer install --no-dev --optimize-autoloader

# 2. Mise à jour de la base de données
php bin/console doctrine:migrations:migrate --no-interaction

# 3. Warmup du cache
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# 4. Compilation des assets
# (Suivant si on utilise AssetMapper ou Webpack Encore)
php bin/console asset-mapper:compile

# 5. Redémarrage du worker Messenger (si applicable)
php bin/console messenger:stop-workers

echo "--- Déploiement terminé avec succès ! ---"
