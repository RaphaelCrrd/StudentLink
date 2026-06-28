<?php

session_start();

require_once '../Model/Database.php';
require_once '../Model/UserModel.php';
require_once '../Model/ReportModel.php';
require_once '../Model/LogModel.php';
require_once '../Model/SchoolModel.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../View/login.php');
    exit();
}

$db = Database::getConnection();

$userModel = new UserModel();
$reportModel = new ReportModel();
$logModel = new LogModel();
$schoolModel = new SchoolModel();

// Action modération
if (isset($_GET['action'], $_GET['user_id']) && $_GET['action'] === 'toggle_status') {

    $userId = intval($_GET['user_id']);
    $reportId = $_GET['report_id'] ?? null;

    $db = Database::getConnection();

    $stmt = $db->prepare("SELECT status, firstname, lastname FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();

    if ($user) {
        $newStatus = ($user['status'] === 'active') ? 'suspended' : 'active';

        $update = $db->prepare("UPDATE users SET status = :status WHERE id = :id");
        $update->execute([
            'status' => $newStatus,
            'id' => $userId
        ]);

        if ($reportId) {
            $reportModel->resolveReport($reportId);
        }

        $logModel->add(
            $newStatus === 'suspended' ? 'COMPTE_SUSPENDU' : 'COMPTE_REACTIVE',
            "Admin #{$_SESSION['user_id']} a modifié le statut de {$user['firstname']} {$user['lastname']} → $newStatus"
        );
    }

    header('Location: AdminController.php?success=1');
    exit();
}

// Data page admin
$users = $db->query("
    SELECT u.id, u.firstname, u.lastname, u.email, u.status, s.name as school_name
    FROM users u
    LEFT JOIN schools s ON u.school_id = s.id
    WHERE u.role != 'admin'
    ORDER BY u.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$logs = $db->query("
    SELECT * FROM logs ORDER BY created_at DESC LIMIT 15
")->fetchAll(PDO::FETCH_ASSOC);

$reports = $reportModel->getPendingReports();

require_once '../View/admin.php';