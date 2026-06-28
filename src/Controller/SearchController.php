<?php

session_start();

require_once '../Model/Database.php';
require_once '../Model/UserModel.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../View/login.php');
    exit();
}

$searchQuery = trim($_GET['q'] ?? '');

$userModel = new UserModel();
$results = [];

if ($searchQuery !== '') {
    $results = $userModel->search($searchQuery, $_SESSION['user_id']);
}

require_once '../View/search-results.php';