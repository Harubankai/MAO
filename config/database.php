<?php
// Database configuration - edit to match your MySQL setup
const DB_HOST = 'localhost';
const DB_NAME = 'moncada_agri';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            exit('Database connection failed. Check config/database.php and import database/schema.sql.');
        }
    }
    return $pdo;
}
const MYSQLDUMP_PATH = 'mysqldump'; // full path on Windows, e.g. C:/xampp/mysql/bin/mysqldump.exe
