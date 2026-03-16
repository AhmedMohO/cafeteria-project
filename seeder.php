<?php

require_once __DIR__ . '/vendor/autoload.php';

// Read .env
$env = parse_ini_file(__DIR__ . '/.env');

$host = $env['DB_HOST'] ?? 'localhost';
$dbName = $env['DB_NAME'] ?? 'cafeteria';
$user = $env['DB_USER'] ?? 'root';
$pass = $env['DB_PASS'] ?? '';
$charset = $env['DB_CHARSET'] ?? 'utf8';

try {
    // Step 1: create database if needed
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8 COLLATE utf8_general_ci");
    echo "✔ Database `$dbName` ready.\n";

    // Step 2: Switch to the target database
    $pdo->exec("USE `$dbName`");

    // Step 3: Run schema (cafeteria.sql)
    $schemaFile = __DIR__ . '/cafeteria.sql';
    if (!file_exists($schemaFile)) {
        die("✘ cafeteria.sql not found!\n");
    }
    $pdo->exec(file_get_contents($schemaFile));
    echo "✔ Schema created from cafeteria.sql.\n";

    // Step 4: Truncate existing data
    echo "  Clearing existing data...\n";
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach (['order_items', 'orders', 'products', 'categories', 'users', 'rooms'] as $table) {
        $pdo->exec("TRUNCATE TABLE `$table`");
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo "✔ Tables truncated.\n";

    // Step 5: Seed sample data
    $dataFile = __DIR__ . '/sampleData.sql';
    if (!file_exists($dataFile)) {
        die("✘ sampleData.sql not found!\n");
    }
    $pdo->exec(file_get_contents($dataFile));
    echo "✔ Sample data seeded from sampleData.sql.\n";

    echo "\n✅ Database setup completed successfully!\n";
    echo "   → Admin login: admin@cafeteria.com / password: 123456\n";

} catch (\PDOException $e) {
    echo "✘ Error: " . $e->getMessage() . "\n";
}
