<?php
/**
 * Database Initialization Script
 * Creates SQLite database for demo purposes
 */

require_once __DIR__ . '/config/database.php';

// Create SQLite database if it doesn't exist
if (defined('USE_SQLITE') && USE_SQLITE) {
    $dbFile = DB_NAME;
    
    if (!file_exists($dbFile)) {
        echo "Creating SQLite database at: $dbFile\n";
        
        try {
            $pdo = new PDO('sqlite:' . $dbFile);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Read and execute the SQLite schema
            $sql = file_get_contents(__DIR__ . '/sql/database_sqlite.sql');
            $pdo->exec($sql);
            
            echo "Database created successfully!\n";
            echo "Default admin user created:\n";
            echo "Email: admin@empresa.com\n";
            echo "Password: admin123\n";
            
        } catch (PDOException $e) {
            echo "Error creating database: " . $e->getMessage() . "\n";
            exit(1);
        }
    } else {
        echo "Database already exists at: $dbFile\n";
    }
} else {
    echo "This script is only for SQLite demo setup.\n";
    echo "For MySQL setup, run the sql/database.sql script manually.\n";
}