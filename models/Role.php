<?php
// models/Role.php
class Role {
    private $db;

    public function __construct() {
        // Ce modèle gère les rôles, qui sont sur la BD locale.
        $this->db = Database::getConnection('local');
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT id, name FROM role ORDER BY name");
        return $stmt->fetchAll();
    }
}