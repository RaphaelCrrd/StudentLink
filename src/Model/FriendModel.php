<?php

class FriendModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function sendRequest($senderId, $receiverId) {
        $stmt = $this->db->prepare("
            INSERT INTO friendships (sender_id, receiver_id, status)
            VALUES (:sender, :receiver, 'pending')
        ");

        return $stmt->execute([
            'sender' => $senderId,
            'receiver' => $receiverId
        ]);
    }

    public function accept($friendshipId, $userId) {
        $stmt = $this->db->prepare("
            UPDATE friendships 
            SET status = 'accepted'
            WHERE id = :id AND receiver_id = :user_id
        ");

        return $stmt->execute([
            'id' => $friendshipId,
            'user_id' => $userId
        ]);
    }
}