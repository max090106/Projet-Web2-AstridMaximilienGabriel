<?php
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'efrei_rdv');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            // Port 3307 = port MySQL de MAMP Windows
            $dsn = "mysql:host=127.0.0.1;port=3307;dbname=" . DB_NAME . ";charset=utf8";

            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            ]);
        } catch (PDOException $e) {
            die(json_encode([
                'success' => false,
                'message' => 'Erreur BDD : ' . $e->getMessage()
            ]));
        }
    }
    return $pdo;
}