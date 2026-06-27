<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Interdit si pas connecté
    exit();
}

try {
    $bdd = new PDO('mysql:host=localhost;dbname=student_link;charset=utf8', 'root', 'root');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    http_response_code(500);
    exit();
}

$action = $_GET['action'] ?? '';

if ($action === 'send') {
    $receiverId = intval($_GET['to']);
    $senderId = $_SESSION['user_id'];

    if ($receiverId > 0 && $receiverId !== $senderId) {
        try {
            $stmt = $bdd->prepare("INSERT INTO friendships (sender_id, receiver_id, status) VALUES (:sender, :receiver, 'pending')");
            $stmt->execute(['sender' => $senderId, 'receiver' => $receiverId]);
        } catch (Exception $e) {
            // Si la demande existe déjà, la clé unique SQL bloque le doublon
        }
    }
    
    http_response_code(200); // Réponse positive
    exit();
}

if ($action === 'accept') {
    $friendshipId = intval($_GET['id']);
    
    $stmt = $bdd->prepare("UPDATE friendships SET status = 'accepted' WHERE id = :id AND receiver_id = :my_id");
    $stmt->execute(['id' => $friendshipId, 'my_id' => $_SESSION['user_id']]);

    header('Location: ../View/profile.php');
    exit();
}