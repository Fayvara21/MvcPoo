<?php
class Database
{
    private static $instance = null;
    private $pdo;
    private function __construct()
    {
        $config = include __DIR__ . "/../config.php";
        $dsn = "mysql:host={$config["db_host"]};dbname={$config["db_name"]}";
        $this->pdo = new PDO($dsn, $config["db_user"], $config["db_pass"]);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function getPdo()
    {
        return $this->pdo;
    }
}

