<?php

session_start();

require_once '../Model/Database.php';
require_once '../Model/UserModel.php';
require_once '../Model/LogModel.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../View/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/setting.php');
    exit();
}

$action = $_POST['action'] ?? '';

$db = Database::getConnection();
$userModel = new UserModel();
$logModel = new LogModel();

if ($action === 'update_password') {

    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if (!$oldPassword || !$newPassword) {
        header('Location: ../View/setting.php?error=empty');
        exit();
    }

    $stmt = $db->prepare("SELECT password FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($oldPassword, $user['password'])) {
        header('Location: ../View/setting.php?error=wrong_password');
        exit();
    }

    $newHash = password_hash($newPassword, PASSWORD_BCRYPT);

    $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
    $stmt->execute([
        'password' => $newHash,
        'id' => $_SESSION['user_id']
    ]);

    $logModel->add('PASSWORD_CHANGE', "User #{$_SESSION['user_id']} a changé son mot de passe");

    header('Location: ../View/setting.php?status=password_updated');
    exit();
}

if ($action === 'disable_account') {

    $stmt = $db->prepare("
        UPDATE users 
        SET status = 'disabled' 
        WHERE id = :id
    ");

    $stmt->execute(['id' => $_SESSION['user_id']]);

    $logModel->add('ACCOUNT_DISABLED', "User #{$_SESSION['user_id']} a désactivé son compte");

    session_unset();
    session_destroy();

    header('Location: ../View/login.php?status=account_disabled');
    exit();
}