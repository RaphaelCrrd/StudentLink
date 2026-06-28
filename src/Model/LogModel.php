<?php

class LogModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function add($type, $description) {
        $stmt = $this->db->prepare("
            INSERT INTO logs (action_type, description)
            VALUES (:type, :desc)
        ");

        return $stmt->execute([
            'type' => $type,
            'desc' => $description
        ]);
    }
}