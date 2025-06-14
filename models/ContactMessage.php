<?php
// models/ContactMessage.php

class ContactMessage {
    private $db;

    public function __construct() {
        // Les messages de contact sont spécifiques à ce site, donc on utilise la BD locale.
        $this->db = Database::getConnection('local');
    }

    /**
     * Enregistre un nouveau message de contact dans la base de données.
     *
     * @param string $name
     * @param string $email
     * @param string $subject
     * @param string $message
     * @return bool True si l'insertion a réussi, false sinon.
     */
    public function save($name, $email, $subject, $message) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO contact_messages (name, email, subject, message) 
                VALUES (?, ?, ?, ?)
            ");
            return $stmt->execute([$name, $email, $subject, $message]);
        } catch (Exception $e) {
            // Enregistre l'erreur dans les logs du serveur pour le débogage.
            error_log("Erreur lors de la sauvegarde du message de contact: " . $e->getMessage());
            return false;
        }
    }
}