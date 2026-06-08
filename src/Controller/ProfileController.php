<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: ../View/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupération et nettoyage
    $age = intval($_POST['age']);
    $interests = htmlspecialchars(trim($_POST['interests']));

    if ($age > 0) {
        try {
            $bdd = new PDO('mysql:host=localhost;dbname=student_link;charset=utf8', 'root', 'root');
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE users SET age = :age, interests = :interests WHERE id = :id";
            $stmt = $bdd->prepare($sql);
            
            $stmt->execute([
                'age' => $age,
                'interests' => $interests,
                'id' => $_SESSION['user_id']
            ]);

            header('Location: ../View/profile.php?update=success');
            exit();

        } catch (Exception $e) {
            die('Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    } else {
        die('Veuillez entrer un âge valide.');
    }
} else {
    header('Location: ../View/profile.php');
    exit();
}