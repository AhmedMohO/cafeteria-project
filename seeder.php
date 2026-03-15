<?php

require_once __DIR__ . '/vendor/autoload.php';

use Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting database seeding...\n";

    // Disable foreign key checks for truncation
    $db->exec('SET FOREIGN_KEY_CHECKS = 0');
    
    // Truncate tables
    $db->exec('TRUNCATE TABLE order_items');
    $db->exec('TRUNCATE TABLE orders');
    $db->exec('TRUNCATE TABLE products');
    $db->exec('TRUNCATE TABLE categories');
    $db->exec('TRUNCATE TABLE users');
    $db->exec('TRUNCATE TABLE rooms');
    
    // Read and execute sampleData.sql
    $sqlFile = __DIR__ . '/sampleData.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        $db->exec($sql);
        echo "Data seeded from sampleData.sql.\n";
    } else {
        echo "sampleData.sql not found!\n";
    }

    // Enable foreign key checks
    $db->exec('SET FOREIGN_KEY_CHECKS = 1');

    echo "Database seeding completed successfully!\n";

} catch (\PDOException $e) {
    echo "Error seeding database: " . $e->getMessage() . "\n";
}
