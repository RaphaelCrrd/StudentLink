<?php

session_start();

require_once '../Model/Database.php';
require_once '../Model/UserModel.php';

$userModel = new UserModel();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../View/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/profile.php');
    exit();
}

$age = intval($_POST['age']);
$interests = htmlspecialchars(trim($_POST['interests']));
$instagram = htmlspecialchars(trim($_POST['instagram'] ?? ''));

if ($age <= 0) {
    die("Âge invalide");
}
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $fileInfo = pathinfo($_FILES['avatar']['name']);
    $extension = strtolower($fileInfo['extension']);

    if (in_array($extension, $allowedExtensions)) {
        if ($_FILES['avatar']['size'] <= 2 * 1024 * 1024) {
            
            $newFilename = 'avatar_' . uniqid() . '.' . $extension;
            $uploadDir = '../../public/uploads/profile_picture/';
            
            $currentUserData = $userModel->getUserById($_SESSION['user_id']);
            
            if (!empty($currentUserData['avatar'])) {
                $oldFileLink = $uploadDir . $currentUserData['avatar'];
                if (file_exists($oldFileLink)) {
                    unlink($oldFileLink); // Supprime
                }
            }

            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $newFilename)) {
                $userModel->updateAvatar($_SESSION['user_id'], $newFilename);
            }
            
        } else {
            die("L'image est trop lourde (maximum 2 Mo).");
        }
    } else {
        die("Format d'image non autorisé.");
    }
}


$userModel->updateProfile($_SESSION['user_id'], $age, $interests, $instagram);

header('Location: ../View/profile.php?update=success');
exit();