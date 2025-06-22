<?php
$host = 'localhost';
$db   = 'alshifa_training';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Ensure default admin user exists every time
    $hashDefault = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT INTO users (fullname,email,department,username,password,role)
VALUES ('Administrator','admin@health.gov.om','IT','admin','$hashDefault','admin')
ON DUPLICATE KEY UPDATE password = VALUES(password);");
} catch (PDOException $e) {
    // If database does not exist, create it automatically then import schema
    if ($e->getCode() == 1049) { // Unknown database
        try {
            // Connect without specifying DB
            $pdoRoot = new PDO("mysql:host=$host;charset=$charset", $user, $pass, $options);
            // Create database
            $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            // Reconnect to the newly created DB
            $pdo = new PDO($dsn, $user, $pass, $options);
            // Import schema file if exists
            $schemaFile = __DIR__ . '/training_schema.sql';
            if (file_exists($schemaFile)) {
                $schemaSql = file_get_contents($schemaFile);
                // Split by ; except inside comments or strings - simple approach
                foreach (array_filter(array_map('trim', explode(";", $schemaSql))) as $statement) {
                    if ($statement !== '') {
                        $pdo->exec($statement);
                    }
                }
            }
            // Ensure default admin user exists
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->exec("INSERT INTO users (fullname,email,department,username,password,role) SELECT 'Administrator','admin@health.gov.om','IT','admin','$hash','admin' WHERE NOT EXISTS (SELECT 1 FROM users WHERE username='admin');");
        } catch (PDOException $ex) {
            die('Database Auto-Setup Failed: ' . $ex->getMessage());
        }
    } else {
        die('Database Connection Failed: ' . $e->getMessage());
    }
}
?>
