<?php

session_start();

require_once '../Model/Database.php';
require_once '../Model/UserModel.php';

$userModel = new UserModel();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/login.php');
    exit();
}

$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$password = $_POST['password'];

if (!$email || !$password) {
    die("Champs invalides");
}

$user = $userModel->findByEmail($email);

if (!$user || !password_verify($password, $user['password'])) {
    die("Identifiants incorrects");
}

if ($user['status'] === 'suspended') {
    die("Compte suspendu");
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['firstname'] = $user['firstname'];
$_SESSION['role'] = $user['role'];

header('Location: ' . ($user['role'] === 'admin'
    ? '../View/admin.php'
    : '../View/dashboard.php'
));

exit();