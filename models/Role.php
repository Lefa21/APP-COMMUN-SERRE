<?php
// models/Role.php
class Role {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT id, name FROM role ORDER BY name");
        return $stmt->fetchAll();
    }
}