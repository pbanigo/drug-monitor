<?php
// Returns a shared PDO connection to the MySQL database.
function get_db()
{
    static $pdo = null;

    if ($pdo === null) {
        $config = require __DIR__ . '/../config.php';

        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}";

        try {
            $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            exit('Database connection failed. Please check config.php.');
        }
    }

    return $pdo;
}
