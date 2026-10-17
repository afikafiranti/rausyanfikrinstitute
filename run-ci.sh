#!/usr/bin/env bash
set -euo pipefail
cd -- "$(dirname -- "${BASH_SOURCE[0]}")"

if [[ ! "${TEST_DB_DATABASE:-}" =~ ^[a-zA-Z0-9_]+_testing$ || -z "${TEST_DB_USERNAME:-}" ]]; then
    printf 'Set TEST_DB_DATABASE to a dedicated database ending in _testing and TEST_DB_USERNAME to its user.\n' >&2
    exit 1
fi
if [[ ! "${TEST_DB_CONNECTION:-mysql}" =~ ^(mysql|mariadb)$ ]]; then
    printf 'TEST_DB_CONNECTION must be mysql or mariadb.\n' >&2
    exit 1
fi

# Ignore application config caches without deleting or rewriting local caches.
export APP_CONFIG_CACHE="$PWD/bootstrap/cache/config.ci.php"
if [[ -e "$APP_CONFIG_CACHE" ]]; then
    printf 'Remove the stale config.ci.php test cache before running CI.\n' >&2
    exit 1
fi

export APP_ENV=testing APP_DEBUG=false APP_MAINTENANCE_DRIVER=file
export DB_CONNECTION="${TEST_DB_CONNECTION:-mysql}"
export DB_HOST="${TEST_DB_HOST:-127.0.0.1}" DB_PORT="${TEST_DB_PORT:-3306}"
export DB_DATABASE="$TEST_DB_DATABASE" DB_USERNAME="$TEST_DB_USERNAME" DB_PASSWORD="${TEST_DB_PASSWORD:-}"
export DB_URL= DB_SOCKET=
export CACHE_STORE=array SESSION_DRIVER=array QUEUE_CONNECTION=sync MAIL_MAILER=array
# A fresh test-only key stays in this process; real environment keys are untouched.
export APP_KEY
APP_KEY="$(php -r 'echo "base64:".base64_encode(random_bytes(32));')"

composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader
composer validate --strict
composer check-platform-reqs
php scripts/assert-test-database.php
npm ci
npm run build
php artisan route:list
# RefreshDatabase handles migrations in the isolated database; no account seeding.
php artisan test --env=testing
