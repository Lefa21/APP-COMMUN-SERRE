<?php
// config/Database.php
class Database {
    private static $instances = [];
    private $connection;
    private static $configs = [
     /*
        'remote' => [
            'host' => '185.216.26.53',
            'dbname' => 'app_g3',
            'user' => 'g3',
            'pwd' => 'azertyg3'
        ],
        */
                
/*
        'remote' => [
            'host' => 'localhost',
            'dbname' => '8PDuqiQ06b_bdd_serre',
            'user' => 'root',
            'pwd' => ''
        ],

        'local' => [
            'host' => 'localhost',
            'dbname' => '8PDuqiQ06b_bdd_serre',
            'user' => 'root',
            'pwd' => ''
        ]
            */
        
        'remote' => [
            'host' => 'herogu.garageisep.com',
            'dbname' => 'm0Vewl0gM0_green_puls',
            'user' => 'IyckUxk4yF_green_puls',
            'pwd' => 'EclQFY8uDGFY6Vbv'
        ],

        'local' => [
            'host' => 'herogu.garageisep.com',
            'dbname' => 'm0Vewl0gM0_green_puls',
            'user' => 'IyckUxk4yF_green_puls',
            'pwd' => 'EclQFY8uDGFY6Vbv'
        ]
    ];

    private function __construct($config) {
        try {
            $this->connection = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
                $config['user'],
                $config['pwd'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            // Gérer l'erreur de manière plus propre en production
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    /**
     * Récupère une instance de connexion de base de données par son nom.
     * @param string $name 'local' ou 'remote'
     * @return PDO
     */
    public static function getConnection($name = 'local') {
        if (!isset(self::$configs[$name])) {
            throw new Exception("La configuration de base de données '$name' n'existe pas.");
        }

        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new self(self::$configs[$name]);
        }
        
        return self::$instances[$name]->connection;
    }

    // Empêcher le clonage
    private function __clone() {}
}