<?php

// Integration rehearsal using synthetic data only; never accepts an existing database.
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

require dirname(__DIR__).'/vendor/autoload.php';
require __DIR__.'/RecoveryCleanup.php';

$options = getopt('', ['run-local-drill', 'mysql-bin:']);
if (! isset($options['run-local-drill'], $options['mysql-bin'])) {
    fwrite(STDERR, "Usage: php scripts/rehearse-recovery.php --run-local-drill --mysql-bin=<directory containing mysql and mysqldump>\n");
    exit(2);
}
$root = realpath(dirname(__DIR__));
$bin = realpath($options['mysql-bin']);
$suffix = PHP_OS_FAMILY === 'Windows' ? '.exe' : '';
$client = $bin ? $bin.'/mysql'.$suffix : '';
$dump = $bin ? $bin.'/mysqldump'.$suffix : '';
if (! is_file($client) || ! is_file($dump) || ! extension_loaded('zip')) {
    fwrite(STDERR, "mysql, mysqldump and PHP zip are required. No database was modified.\n");
    exit(2);
}
$config = [];
foreach (simplexml_load_file($root.'/phpunit.xml')->php->env as $entry) {
    if (str_starts_with((string) $entry['name'], 'DB_')) {
        $config[(string) $entry['name']] = (string) $entry['value'];
    }
}
if (($config['DB_CONNECTION'] ?? '') !== 'mysql' || ($config['DB_HOST'] ?? '') !== '127.0.0.1') {
    throw new RuntimeException('Rehearsal requires the local MySQL test configuration.');
}
$id = bin2hex(random_bytes(6));
$source = 'rf_b_recovery_'.$id.'_source_testing';
$target = 'rf_b_recovery_'.$id.'_restore_testing';
$work = $root.'/storage/app/recovery-drill-'.$id;
$created = [];
$pdo = null;
$started = microtime(true);
$report = null;
$assert = function (bool $condition, string $message): void {
    if (! $condition) { throw new RuntimeException($message); }
};
$run = function (array $command, $input = null): void {
    $process = new Process($command);
    $process->setTimeout(120);
    if ($input !== null) { $process->setInput($input); }
    $process->mustRun();
};

try {
    $assert(mkdir($work, 0700), 'Cannot create a fresh rehearsal directory');
    $pdo = new PDO('mysql:host=127.0.0.1;port='.$config['DB_PORT'], $config['DB_USERNAME'], $config['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    foreach ([$source, $target] as $database) {
        $assert((bool) preg_match('/\Arf_b_recovery_[a-f0-9]{12}_(source|restore)_testing\z/', $database), 'Unsafe database name');
        $pdo->exec('CREATE DATABASE `'.$database.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $created[] = $database;
    }
    $key = 'base64:'.base64_encode(random_bytes(32));
    $environment = array_merge($config, ['APP_ENV' => 'testing', 'APP_DEBUG' => 'false', 'APP_KEY' => $key, 'DB_DATABASE' => $source, 'DB_URL' => '', 'DB_SOCKET' => '', 'APP_CONFIG_CACHE' => $work.'/absent-config.php', 'CACHE_STORE' => 'array', 'SESSION_DRIVER' => 'array', 'QUEUE_CONNECTION' => 'sync', 'MAIL_MAILER' => 'array']);
    foreach ($environment as $name => $value) { putenv($name.'='.$value); $_ENV[$name] = $_SERVER[$name] = $value; }
    $app = require $root.'/bootstrap/app.php';
    $app->loadEnvironmentFrom('.env.recovery-rehearsal-absent');
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    set_exception_handler(function (Throwable $exception): void {
        fwrite(STDERR, 'Recovery rehearsal failed: '.$exception->getMessage().PHP_EOL);
        exit(1);
    });
    Tests\Support\TestDatabaseSafety::assertSafe($app->environment(), $app['db']->connection()->getDriverName(), $app['db']->connection()->getDatabaseName());
    $assert(Artisan::call('migrate', ['--force' => true]) === 0, 'Source migrations failed');
    $region = App\Models\Wilayah::create(['name' => 'Recovery fixture']);
    $author = App\Models\User::factory()->create(['status' => 'active', 'wilayah_id' => $region->id]);
    $reviewer = App\Models\User::factory()->create(['status' => 'active', 'wilayah_id' => $region->id]);
    $author->roles()->attach(App\Models\Role::create(['name' => 'alumni']));
    $reviewer->roles()->attach(App\Models\Role::create(['name' => 'admin']));
    $cipher = Crypt::encryptString('synthetic-recovery-proof');
    App\Models\Post::create(['title' => 'Recovery fixture post', 'content' => 'Synthetic content', 'cover_url' => 'covers/drill.jpg', 'published_at' => now()]);
    App\Models\Gallery::create(['title' => 'Recovery fixture gallery', 'image_url' => 'galleries/drill.jpg']);
    $record = App\Models\KajianReport::create(['user_id' => $author->id, 'wilayah_id' => $region->id, 'tanggal' => '2026-10-17', 'judul' => 'Recovery fixture report', 'status' => 'approved', 'peserta_l' => 2, 'peserta_p' => 3, 'total_peserta' => 5, 'reviewed_by' => $reviewer->id, 'reviewed_at' => now(), 'foto_url' => 'report-photos/drill.png', 'catatan' => $cipher]);
    $expected = [];
    foreach (['migrations', 'users', 'roles', 'role_user', 'wilayah', 'posts', 'galleries', 'kajian_reports'] as $table) {
        $expected[$table] = $app['db']->table($table)->count();
    }
    $quote = fn (string $value) => '"'.str_replace(["\\", '"', "\r", "\n"], ["\\\\", '\\"', '\\r', '\\n'], $value).'"';
    file_put_contents($work.'/client.cnf', "[client]\nhost=127.0.0.1\nport=".$config['DB_PORT']."\nuser=".$quote($config['DB_USERNAME'])."\npassword=".$quote($config['DB_PASSWORD'])."\n");
    chmod($work.'/client.cnf', 0600);
    $run([$dump, '--defaults-extra-file='.$work.'/client.cnf', '--single-transaction', '--routines', '--triggers', '--events', '--hex-blob', '--no-tablespaces', '--set-gtid-purged=OFF', '--result-file='.$work.'/database.sql', $source]);
    $assert(filesize($work.'/database.sql') > 0, 'Empty SQL backup');
    $files = ['storage/app/public/covers/drill.jpg' => random_bytes(256), 'storage/app/public/galleries/drill.jpg' => random_bytes(256), 'storage/app/private/report-photos/drill.png' => random_bytes(256), 'environment.key' => $key];
    $hashes = ['database.sql' => hash_file('sha256', $work.'/database.sql')];
    $zip = new ZipArchive;
    $assert($zip->open($work.'/backup.zip', ZipArchive::CREATE) === true, 'Cannot create archive');
    $zip->addFile($work.'/database.sql', 'database.sql');
    foreach ($files as $path => $bytes) { $zip->addFromString($path, $bytes); $hashes[$path] = hash('sha256', $bytes); }
    $zip->addFromString('manifest.json', json_encode($hashes, JSON_THROW_ON_ERROR));
    $assert($zip->close(), 'Cannot finish archive');
    $backupHash = hash_file('sha256', $work.'/backup.zip');
    $restoreStarted = microtime(true);
    $assert($zip->open($work.'/backup.zip') === true, 'Cannot open archive');
    $assert($zip->extractTo($work.'/restored'), 'Cannot restore archive');
    $zip->close();
    $manifest = json_decode(file_get_contents($work.'/restored/manifest.json'), true, flags: JSON_THROW_ON_ERROR);
    foreach ($manifest as $path => $hash) { $assert(hash_equals($hash, hash_file('sha256', $work.'/restored/'.$path)), 'Restored checksum mismatch'); }
    // A damaged backup must be rejected before SQL import.
    $assert(! hash_equals($manifest['database.sql'], hash('sha256', file_get_contents($work.'/restored/database.sql').'damage')), 'Corruption check failed');
    $input = fopen($work.'/restored/database.sql', 'r');
    try { $run([$client, '--defaults-extra-file='.$work.'/client.cnf', '--default-character-set=utf8mb4', $target], $input); } finally { fclose($input); }
    $restored = new PDO('mysql:host=127.0.0.1;port='.$config['DB_PORT'].';dbname='.$target, $config['DB_USERNAME'], $config['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    foreach ($expected as $table => $count) { $assert((int) $restored->query('SELECT COUNT(*) FROM `'.$table.'`')->fetchColumn() === $count, 'Restored row count mismatch: '.$table); }
    $row = $restored->query('SELECT * FROM kajian_reports WHERE id='.(int) $record->id)->fetch(PDO::FETCH_ASSOC);
    $assert($row['status'] === 'approved' && (int) $row['total_peserta'] === 5 && (int) $row['reviewed_by'] === $reviewer->id && $row['reviewed_at'] !== null, 'Review metadata mismatch');
    $roleQuery = $restored->query('SELECT roles.name FROM role_user JOIN roles ON roles.id=role_user.role_id WHERE role_user.user_id='.(int) $reviewer->id);
    $assert($roleQuery->fetchColumn() === 'admin', 'Reviewer role association missing');
    $assert(is_file($work.'/restored/storage/app/private/'.$row['foto_url']), 'Private report photo missing');
    $restoredEncrypter = new Illuminate\Encryption\Encrypter(base64_decode(substr(file_get_contents($work.'/restored/environment.key'), 7)), 'AES-256-CBC');
    $assert($restoredEncrypter->decryptString($row['catatan']) === 'synthetic-recovery-proof', 'Restored key cannot decrypt');
    $report = ['status' => 'PASS', 'runtime' => PHP_VERSION, 'database_server' => $pdo->query('SELECT VERSION()')->fetchColumn(), 'synthetic_only' => true, 'row_counts' => $expected, 'files_verified' => count($files) - 1, 'key_decryption' => 'PASS', 'corruption_detection' => 'PASS', 'archive_sha256' => $backupHash, 'restore_seconds' => round(microtime(true) - $restoreStarted, 3), 'total_seconds' => round(microtime(true) - $started, 3)];
} finally {
    $cleanup = [];
    foreach ($created as $database) {
        if (in_array($database, [$source, $target], true) && preg_match('/\Arf_b_recovery_[a-f0-9]{12}_(source|restore)_testing\z/', $database)) {
            $cleanup[$database] = fn () => $pdo->exec('DROP DATABASE `'.$database.'`');
        }
    }
    $cleanup['temporary credential and backup files'] = function () use ($work, $root, $id): void {
        $resolved = realpath($work);
        if ($resolved && str_starts_with($resolved, $root.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR) && basename($resolved) === 'recovery-drill-'.$id) {
            if (! (new Filesystem)->deleteDirectory($resolved)) { throw new RuntimeException('Temporary rehearsal directory cleanup failed'); }
        } elseif ($resolved) {
            throw new RuntimeException('Unsafe cleanup path');
        }
    };
    RecoveryCleanup::run($cleanup);
}
$report['cleanup'] = 'PASS';
echo json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
