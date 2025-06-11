<?php
// config/Database.php
class Database {
    private static $instance = null;
    private $connection;
    
    // Configuration XAMPP
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = '8PDuqiQ06b_bdd_serre'; // Votre base de données existante
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Méthode pour adapter les données existantes et ajouter les tables manquantes
    public function createTables() {
        // Ajouter les tables manquantes pour compléter le système
        $sql = "
        CREATE TABLE IF NOT EXISTS teams (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            greenhouse_sector VARCHAR(50) NOT NULL
        );
        
        -- Ajouter des colonnes manquantes aux tables existantes
        ALTER TABLE capteurs 
        ADD COLUMN IF NOT EXISTS team_id INT DEFAULT 1 AFTER unite,
        ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE AFTER team_id,
        ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER is_active;
        
        ALTER TABLE actionneurs 
        ADD COLUMN IF NOT EXISTS team_id INT DEFAULT 1 AFTER type,
        ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE AFTER team_id,
        ADD COLUMN IF NOT EXISTS current_state BOOLEAN DEFAULT FALSE AFTER is_active,
        ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER current_state;
        
        -- Table pour les logs d'actions des actionneurs (en plus de etats_actionneurs)
        CREATE TABLE IF NOT EXISTS actuator_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            actionneur_id INT NOT NULL,
            action ENUM('ON', 'OFF') NOT NULL,
            user_id VARCHAR(36) NOT NULL,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (actionneur_id) REFERENCES actionneurs(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES user(id_user) ON DELETE CASCADE
        );
        ";
        
        try {
            $this->connection->exec($sql);
        } catch (PDOException $e) {
            // Les colonnes existent peut-être déjà, continuer
            error_log("Erreur SQL (probablement normale): " . $e->getMessage());
        }
        
        // Insérer des données de test si nécessaire
        $this->insertTestData();
    }
    
    private function insertTestData() {
        // Vérifier si des équipes existent déjà
        $stmt = $this->connection->query("SELECT COUNT(*) FROM teams");
        if ($stmt->fetchColumn() == 0) {
            $this->connection->exec("
                INSERT INTO teams (name, greenhouse_sector) VALUES 
                ('Équipe Alpha', 'Secteur A'),
                ('Équipe Beta', 'Secteur B'),
                ('Équipe Gamma', 'Secteur C'),
                ('Équipe Delta', 'Secteur D'),
                ('Équipe Epsilon', 'Secteur E');
            ");
        }
        
        // Vérifier s'il faut ajouter des capteurs et actionneurs de test
        $stmt = $this->connection->query("SELECT COUNT(*) FROM capteurs");
        if ($stmt->fetchColumn() == 0) {
            $this->connection->exec("
                INSERT INTO capteurs (nom, type, unite, team_id) VALUES 
                ('Température Serre A', 'temperature', '°C', 1),
                ('Humidité Serre A', 'humidity', '%', 1),
                ('Humidité Sol A', 'soil_moisture', '%', 1),
                ('Luminosité A', 'light', 'lux', 1),
                ('pH Sol A', 'ph', 'pH', 1);
            ");
        }
        
        $stmt = $this->connection->query("SELECT COUNT(*) FROM actionneurs");
        if ($stmt->fetchColumn() == 0) {
            $this->connection->exec("
                INSERT INTO actionneurs (nom, type, team_id) VALUES 
                ('Arrosage Automatique A', 'irrigation', 1),
                ('Ventilation A', 'ventilation', 1),
                ('Éclairage A', 'lighting', 1),
                ('Chauffage A', 'heating', 1);
            ");
        }
    }
}

// Initialiser la base de données au chargement
$db = Database::getInstance();
$db->createTables();
?>