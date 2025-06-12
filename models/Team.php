<?php
// models/Team.php
class Team {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT id, name, greenhouse_sector FROM teams ORDER BY name");
        return $stmt->fetchAll();
    }
}