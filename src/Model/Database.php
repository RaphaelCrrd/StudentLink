<?php

require_once 'EnvLoader.php';

class Database {
    private static $instance = null;

    public static function getConnection() {
        if (self::$instance === null) {
            EnvLoader::load(__DIR__ . '/../../.env');

            try {
                $host = $_ENV['DB_HOST'];
                $dbname = $_ENV['DB_NAME'];
                $user = $_ENV['DB_USER'];
                $pass = $_ENV['DB_PASS'];

                self::$instance = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $e) {
                die('Erreur de connexion à la base de données : ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}