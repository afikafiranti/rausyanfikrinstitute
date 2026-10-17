#!/usr/bin/env bash
set -euo pipefail

repo_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../.." && pwd)"
temp_parent="$(cd -- "${TMPDIR:-/tmp}" && pwd -P)"
test_root="$(mktemp -d "$temp_parent/rf-ci-test.XXXXXX")"
[[ "$test_root" == "$temp_parent"/rf-ci-test.* ]] || exit 1
trap 'rm -rf -- "$test_root"' EXIT
mkdir -p "$test_root/bin" "$test_root/scripts" "$test_root/bootstrap/cache"
cp "$repo_root/run-ci.sh" "$test_root/run-ci.sh"
printf 'local environment sentinel\n' > "$test_root/.env"
printf 'test environment sentinel\n' > "$test_root/.env.testing"
cp "$test_root/.env" "$test_root/env.before"
cp "$test_root/.env.testing" "$test_root/testing.before"
cp "$repo_root/.env.example" "$test_root/.env.example"
cd -- "$test_root"

cat > "$test_root/bin/stub" <<'STUB'
#!/usr/bin/env bash
set -euo pipefail
tool="${0##*/}"
printf '%s\n' "$tool $*" >> "$COMMAND_LOG"
if [[ "${FAIL_COMMAND:-}" == "$tool $*" ]]; then exit 17; fi
[[ "$APP_ENV" == testing && "$DB_DATABASE" == rf_b_testing && "$DB_USERNAME" == rf_test ]]
[[ -z "$DB_URL" && -z "$DB_SOCKET" ]]
[[ "$CACHE_STORE" == array && "$SESSION_DRIVER" == array && "$QUEUE_CONNECTION" == sync && "$MAIL_MAILER" == array ]]
[[ "$APP_CONFIG_CACHE" == */bootstrap/cache/config.ci.php ]]
if [[ "$tool" == php && "${1:-}" == -r ]]; then printf 'temporary-test-key'; fi
STUB
for tool in php composer npm; do
    cp "$test_root/bin/stub" "$test_root/bin/$tool"
    chmod +x "$test_root/bin/$tool"
done

export PATH="$test_root/bin:$PATH"
export COMMAND_LOG="$test_root/commands.log"
export TEST_DB_DATABASE=rf_b_testing TEST_DB_USERNAME=rf_test
unset FAIL_COMMAND
# Deliberately inherit unsafe values. Runner must use only TEST_DB_* settings.
export DB_DATABASE=production DB_USERNAME=production DB_URL=production DB_SOCKET=production
export APP_ENV=production CACHE_STORE=database SESSION_DRIVER=database QUEUE_CONNECTION=database MAIL_MAILER=smtp

expect_failure() {
    : > "$COMMAND_LOG"
    if bash "$test_root/run-ci.sh" > "$test_root/output.log" 2>&1; then
        printf 'FAIL: runner should reject %s\n' "$1" >&2
        exit 1
    fi
    if [[ -s "$COMMAND_LOG" ]]; then
        printf 'FAIL: unsafe configuration reached external commands\n' >&2
        exit 1
    fi
}

TEST_DB_DATABASE=rf_db expect_failure 'a development database'
TEST_DB_DATABASE= expect_failure 'a missing test database'
TEST_DB_USERNAME= expect_failure 'a missing test user'
TEST_DB_CONNECTION=sqlite expect_failure 'an unsupported database connection'
touch "$test_root/bootstrap/cache/config.ci.php"
expect_failure 'cached configuration'
rm -- "$test_root/bootstrap/cache/config.ci.php"

: > "$COMMAND_LOG"
bash "$test_root/run-ci.sh" > "$test_root/output.log" 2>&1
cat > "$test_root/expected.log" <<'EXPECTED'
php -r echo "base64:".base64_encode(random_bytes(32));
composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader
composer validate --strict
composer check-platform-reqs
php scripts/assert-test-database.php
npm ci
npm run build
php artisan route:list
php artisan test --env=testing
EXPECTED
diff -u "$test_root/expected.log" "$COMMAND_LOG"
cmp "$test_root/env.before" "$test_root/.env"
cmp "$test_root/testing.before" "$test_root/.env.testing"

export FAIL_COMMAND='php scripts/assert-test-database.php'
: > "$COMMAND_LOG"
if bash "$test_root/run-ci.sh" > "$test_root/output.log" 2>&1; then
    printf 'FAIL: runner ignored failed database preflight\n' >&2
    exit 1
fi
if grep -q '^npm\|^php artisan' "$COMMAND_LOG"; then
    printf 'FAIL: runner continued after failed preflight\n' >&2
    exit 1
fi
cmp "$test_root/env.before" "$test_root/.env"
cmp "$test_root/testing.before" "$test_root/.env.testing"
printf 'PASS: runner rejects unsafe config, preserves env, orders commands and stops on preflight failure\n'
