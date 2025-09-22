#!/usr/bin/env bash
set -e

cp .env.example .env
cp .env.example .env.testing

php artisan key:generate --env=testing
php artisan migrate:fresh --env=testing --seed

composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader
npm ci

php artisan test --env=testing -v
