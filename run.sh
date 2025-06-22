#!/bin/bash

set -e

echo "Installing Composer dependencies..."
composer install

echo "Starting Docker Compose..."
docker compose up -d

echo "Building Webpack assets..."
npm run build

echo "Running Doctrine migrations..."
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

echo "Loading fixtures..."
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction

echo "Opening the app in your browser..."
if command -v xdg-open > /dev/null; then
  xdg-open http://localhost:8080
elif command -v open > /dev/null; then
  open http://localhost:8080
else
  echo "Please open http://localhost:8080 in your browser."
fi

echo "All done!"
