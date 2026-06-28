<?php

class SchoolModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM schools ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}