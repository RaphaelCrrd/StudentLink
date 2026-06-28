<?php

session_start();

require_once '../Model/Database.php';
require_once '../Model/FriendModel.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit();
}

$friendModel = new FriendModel();

$action = $_GET['action'] ?? '';

if ($action === 'send') {

    $receiverId = intval($_GET['to']);

    if ($receiverId > 0 && $receiverId !== $_SESSION['user_id']) {
        $friendModel->sendRequest($_SESSION['user_id'], $receiverId);
    }

    http_response_code(200); // Réponse positive
    exit();
}

if ($action === 'accept') {

    $friendshipId = intval($_GET['id']);

    $friendModel->accept($friendshipId, $_SESSION['user_id']);

    header('Location: ../View/profile.php');
    exit();
}