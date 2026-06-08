<?php

session_start();

try {
    $bdd = new PDO('mysql:host=localhost;dbname=student_link;charset=utf8', 'root', 'root'); // A la fin remplacer 'root' par '' sur Windows
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur de connexion à la BDD : ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email    = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if ($email && !empty($password)) {
        
        try {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $bdd->prepare($sql);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                
                if ($user['status'] === 'suspended') {
                    echo "Votre compte a été suspendu par un administrateur.";
                    exit();
                }

                $_SESSION['user_id']   = $user['id'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['role']      = $user['role'];

                header('Location: ../View/dashboard.php');
                exit();

            } else {
                echo "Identifiants incorrects. Veuillez réessayer.";
            }

        } catch (PDOException $e) {
            echo "Une erreur technique est survenue : " . $e->getMessage();
        }

    } else {
        echo "Veuillez entrer un email et un mot de passe valides.";
    }
} else {
    header('Location: ../View/login.php');
    exit();
}