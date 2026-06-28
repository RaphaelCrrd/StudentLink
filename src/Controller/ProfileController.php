<?php

session_start();

require_once '../Model/Database.php';
require_once '../Model/UserModel.php';

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

$userModel = new UserModel();
$userModel->updateProfile($_SESSION['user_id'], $age, $interests, $instagram);

header('Location: ../View/profile.php?update=success');
exit();