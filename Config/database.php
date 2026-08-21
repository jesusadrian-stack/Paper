<?php
/**
 * Conexión a Base de Datos MySQL/MariaDB mediante PDO
 */

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $dbName = env('DB_DATABASE', 'papeleria_corresponsal');
            $username = env('DB_USERNAME', 'root');
            $password = env('DB_PASSWORD', '');

            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            try {
                self::$instance = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                // Si la base de datos no existe aún, intentar conexión sin dbname para scripts de instalación
                try {
                    $dsnNoDb = "mysql:host={$host};port={$port};charset=utf8mb4";
                    return new PDO($dsnNoDb, $username, $password, $options);
                } catch (PDOException $ex) {
                    die("Error crítico de conexión a la base de datos en Laragon: " . $ex->getMessage());
                }
            }
        }

        return self::$instance;
    }

    public static function resetConnection(): void {
        self::$instance = null;
    }
}
