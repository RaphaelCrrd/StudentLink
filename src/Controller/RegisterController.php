<?php
try {
    // Sur Windows, remplacer le dernier 'root' par ''
    $bdd = new PDO('mysql:host=localhost;dbname=student_link;charset=utf8', 'root', 'root');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die('Erreur critique de connexion à la BDD : ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // nettoyage des données pour la sécurité (Anti-XSS)
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname = htmlspecialchars(trim($_POST['lastname']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $age = intval($_POST['age']);
    $password = $_POST['password'];
    $school_id = !empty($_POST['school_id']) ? intval($_POST['school_id']) : null;
    $interests = htmlspecialchars(trim($_POST['interests']));
    $instagram = htmlspecialchars(trim($_POST['instagram']));

    if (!empty($firstname) && !empty($lastname) && $email && !empty($age) && !empty($password) && !empty($school_id)) {
        
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $sql = "INSERT INTO users (firstname, lastname, email, password, age, school_id, interests, instagram, role, status) 
                    VALUES (:firstname, :lastname, :email, :password, :age, :school_id, :interests, :instagram, 'student', 'active')";
            
            $stmt = $bdd->prepare($sql);
            
            $stmt->execute([
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'password' => $passwordHash,
                'age' => $age,
                'school_id' => $school_id,
                'interests' => $interests,
                'instagram' => $instagram,
            ]);

            $logSql = "INSERT INTO logs (action_type, description) VALUES ('INSCRIPTION', :desc)";
            $logStmt = $bdd->prepare($logSql);
            $logStmt->execute(['desc' => "Nouvel étudiant inscrit : " . $firstname . " " . $lastname . " (" . $email . ")"]);

            header('Location: ../View/login.php?registration=success');
            exit();

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo "Erreur : Cette adresse email est déjà associée à un compte.";
            } else {
                $logSql = "INSERT INTO logs (action_type, description) VALUES ('SYSTEM_ERROR', :desc)";
                $logStmt = $bdd->prepare($logSql);
                $logStmt->execute(['desc' => "Erreur SQL : " . $e->getMessage()]);
                
                die('Une erreur technique est survenue.');
            }
        }

    } else {
        echo "Erreur : Veuillez remplir tous les champs correctement (votre email n'est peut-être pas valide).";
    }
} else {
    header('Location: ../View/register.php');
    exit();
}