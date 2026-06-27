<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../View/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['reason']) && !empty($_POST['reported_id'])) {
    
    $reporterId = $_SESSION['user_id'];
    $reportedId = intval($_POST['reported_id']);
    $reason = htmlspecialchars(trim($_POST['reason']));

    if ($reporterId === $reportedId) {
        header('Location: ../View/dashboard.php');
        exit();
    }

    try {
        $bdd = new PDO('mysql:host=localhost;dbname=student_link;charset=utf8', 'root', 'root');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "INSERT INTO reports (reporter_id, reported_id, reason, status) VALUES (:reporter, :reported, :reason, 'pending')";
        $stmt = $bdd->prepare($sql);
        $stmt->execute([
            'reporter' => $reporterId,
            'reported' => $reportedId,
            'reason' => $reason
        ]);

        $logSql = "INSERT INTO logs (action_type, description) VALUES ('SIGNALEMENT', :desc)";
        $logStmt = $bdd->prepare($logSql);
        $logStmt->execute([
            'desc' => "L'utilisateur ID $reporterId a signalé l'utilisateur ID $reportedId pour la raison : $reason"
        ]);

        header("Location: UserProfileController.php?id=" . $reportedId . "&report=success");
        exit();

    } catch (Exception $e) {
        die('Erreur lors du signalement : ' . $e->getMessage());
    }
} else {
    header('Location: ../View/dashboard.php');
    exit();
}