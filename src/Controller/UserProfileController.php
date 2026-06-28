<?php

session_start();

require_once '../Model/Database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../View/login.php');
    exit();
}

$profileId = intval($_GET['id'] ?? 0);
$currentUserId = $_SESSION['user_id'];

if ($profileId <= 0) {
    header('Location: ../View/dashboard.php');
    exit();
}

if ($profileId === $currentUserId) {
    header('Location: ../View/profile.php');
    exit();
}

try {
    $db = Database::getConnection();

    $stmt = $db->prepare("
        SELECT u.id, u.firstname, u.lastname, u.email,
               u.instagram, u.age, u.interests,
               s.name as school_name
        FROM users u
        LEFT JOIN schools s ON u.school_id = s.id
        WHERE u.id = :id AND u.status = 'active'
    ");

    $stmt->execute(['id' => $profileId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Profil introuvable");
    }

    require_once '../View/user-profile.php';

} catch (Exception $e) {
    die("Erreur profil");
}