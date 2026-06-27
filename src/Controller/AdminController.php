<?php
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../View/login.php');
    exit();
}

try {
    $bdd = new PDO('mysql:host=localhost;dbname=student_link;charset=utf8', 'root', 'root');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur de connexion BDD : ' . $e->getMessage());
}

if (isset($_GET['action']) && isset($_GET['user_id'])) {
    
    $action = $_GET['action'];
    $userId = intval($_GET['user_id']);
    $reportId = isset($_GET['report_id']) ? intval($_GET['report_id']) : null;

    if ($action === 'toggle_status') {
        try {
            // Récupération du statut actuel
            $checkStmt = $bdd->prepare("SELECT status, firstname, lastname FROM users WHERE id = :id");
            $checkStmt->execute(['id' => $userId]);
            $user = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $newStatus = ($user['status'] === 'active') ? 'suspended' : 'active';
                
                $updateStmt = $bdd->prepare("UPDATE users SET status = :status WHERE id = :id");
                $updateStmt->execute(['status' => $newStatus, 'id' => $userId]);

                if ($reportId) {
                    $resolvedStmt = $bdd->prepare("UPDATE reports SET status = 'resolved' WHERE id = :report_id");
                    $resolvedStmt->execute(['report_id' => $reportId]);
                }

                // Enregistrement du log
                $logAction = ($newStatus === 'suspended') ? 'COMPTE_SUSPENDU' : 'COMPTE_REACTIVE';
                $logDesc = "L'admin (ID " . $_SESSION['user_id'] . ") a passé le statut de " . $user['firstname'] . " " . $user['lastname'] . " à [" . $newStatus . "].";
                
                $logStmt = $bdd->prepare("INSERT INTO logs (action_type, description) VALUES (:type, :desc)");
                $logStmt->execute(['type' => $logAction, 'desc' => $logDesc]);
            }

            header('Location: AdminController.php?success=1');
            exit();

        } catch (Exception $e) {
            die('Erreur Action Modération : ' . $e->getMessage());
        }
    }
}


try {
    // Récupére tous les users
    $stmt = $bdd->query("SELECT u.id, u.firstname, u.lastname, u.email, u.status, s.name as school_name 
                         FROM users u 
                         LEFT JOIN schools s ON u.school_id = s.id 
                         WHERE u.role != 'admin' 
                         ORDER BY u.id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmtLogs = $bdd->query("SELECT * FROM logs ORDER BY created_at DESC LIMIT 15");
    $logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

    $stmtReports = $bdd->query("
        SELECT r.id as report_id, r.reason, r.status, r.created_at,
               u_reporter.firstname as reporter_fn, u_reporter.lastname as reporter_ln,
               u_reported.id as reported_user_id, u_reported.firstname as reported_fn, u_reported.lastname as reported_ln
        FROM reports r
        JOIN users u_reporter ON r.reporter_id = u_reporter.id
        JOIN users u_reported ON r.reported_id = u_reported.id
        WHERE r.status = 'pending'
        ORDER BY r.created_at DESC
    ");
    $reports = $stmtReports->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die('Erreur Chargement Données Admin : ' . $e->getMessage());
}


require_once '../View/admin.php';