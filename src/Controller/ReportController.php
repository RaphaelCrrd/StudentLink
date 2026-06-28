<?php

session_start();

require_once '../Model/Database.php';
require_once '../Model/LogModel.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../View/login.php');
    exit();
}

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($_POST['reason']) ||
    empty($_POST['reported_id'])
) {
    header('Location: ../View/dashboard.php');
    exit();
}

$reporterId = $_SESSION['user_id'];
$reportedId = intval($_POST['reported_id']);
$reason = htmlspecialchars(trim($_POST['reason']));

if ($reporterId === $reportedId) {
    header('Location: ../View/dashboard.php');
    exit();
}

try {
    $db = Database::getConnection();

    $stmt = $db->prepare("
        INSERT INTO reports (reporter_id, reported_id, reason, status)
        VALUES (:reporter, :reported, :reason, 'pending')
    ");

    $stmt->execute([
        'reporter' => $reporterId,
        'reported' => $reportedId,
        'reason' => $reason
    ]);

    $logModel = new LogModel();
    $logModel->add(
        'SIGNALEMENT',
        "User $reporterId a signalé user $reportedId : $reason"
    );

    header("Location: ../View/user-profile.php?id=$reportedId&report=success");
    exit();

} catch (Exception $e) {
    die("Erreur signalement");
}