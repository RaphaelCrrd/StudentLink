<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../View/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=student_link;charset=utf8', 'root', 'root');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (Exception $e) {
        die('Erreur BDD : ' . $e->getMessage());
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'update_password') {
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';

        if (!empty($oldPassword) && !empty($newPassword)) {
            // Récupère le mot de passe haché actuel en BDD
            $stmt = $bdd->prepare("SELECT password FROM users WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Vérifie si l'ancien mot de passe correspond
            if ($user && password_verify($oldPassword, $user['password'])) {
                
                // Hache le nouveau mot de passe et fait l'UPDATE
                $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
                $updateStmt = $bdd->prepare("UPDATE users SET password = :password WHERE id = :id");
                $updateStmt->execute([
                    'password' => $newHash,
                    'id' => $_SESSION['user_id']
                ]);

                header('Location: ../View/setting.php?status=password_updated');
                exit();
            } else {
                header('Location: ../View/setting.php?error=wrong_password');
                exit();
            }
        } else {
            header('Location: ../View/setting.php?error=empty');
            exit();
        }
    }

    if ($action === 'disable_account') {
        $stmt = $bdd->prepare("UPDATE users SET status = 'disabled' WHERE id = :id"); // Disabled au lieu de delete pour garder une trace en cas de recréation de compte
        $stmt->execute(['id' => $_SESSION['user_id']]);

        session_unset();
        session_destroy();

        header('Location: ../View/login.php?status=account_disabled');
        exit();
    }

} else {
    header('Location: ../View/setting.php');
    exit();
}