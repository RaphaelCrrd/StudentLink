<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../View/login.php');
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../View/dashboard.php');
    exit();
}

$profileId = intval($_GET['id']);
$currentUserId = $_SESSION['user_id'];

// Si l'utilisateur clique sur lui-même, ça le renvoie sur sa propre page profil
if ($profileId === $currentUserId) {
    header('Location: ../View/profile.php');
    exit();
}

try {
    $bdd = new PDO('mysql:host=localhost;dbname=student_link;charset=utf8', 'root', 'root');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $bdd->prepare("
        SELECT u.id, u.firstname, u.lastname, u.email, u.instagram, u.age, u.interests, s.name as school_name 
        FROM users u 
        LEFT JOIN schools s ON u.school_id = s.id 
        WHERE u.id = :id AND u.status = 'active'
    ");
    $stmt->execute(['id' => $profileId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Cet étudiant n'existe pas ou son compte a été suspendu.");
    }

} catch (Exception $e) {
    die('Erreur affichage profil : ' . $e->getMessage());
}

require_once '../View/user-profile.php';