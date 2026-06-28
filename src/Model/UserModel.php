<?php

class UserModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id, $age, $interests, $instagram) {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET age = :age, interests = :interests, instagram = :instagram 
            WHERE id = :id
        ");

        return $stmt->execute([
            'age' => $age,
            'interests' => $interests,
            'instagram' => $instagram,
            'id' => $id
        ]);
    }

    public function search($query, $myId) {
        $stmt = $this->db->prepare("
            SELECT u.id, u.firstname, u.lastname, u.interests, u.instagram, s.name as school_name 
            FROM users u
            LEFT JOIN schools s ON u.school_id = s.id
            WHERE (u.firstname LIKE :q 
                OR u.lastname LIKE :q 
                OR u.interests LIKE :q 
                OR s.name LIKE :q)
            AND u.id != :my_id
            AND u.status = 'active'
            ORDER BY u.lastname ASC
        ");

        $stmt->execute([
            'q' => "%$query%",
            'my_id' => $myId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}