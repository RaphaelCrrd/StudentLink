<?php
try {
    // Sur Windows, remplacer le dernier 'root' par '' (vide)
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

    if (!empty($firstname) && !empty($lastname) && $email && !empty($age) && !empty($password) && !empty($school_id)) {
        
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        try {
            $sql = "INSERT INTO users (firstname, lastname, email, password, age, school_id, interests, role, status) 
                    VALUES (:firstname, :lastname, :email, :password, :age, :school_id, :interests, 'student', 'active')";
            
            $stmt = $bdd->prepare($sql);
            
       
            $stmt->execute([
                'firstname' => $firstname,
                'lastname'  => $lastname,
                'email'     => $email,
                'password'  => $passwordHash,
                'age'       => $age,
                'school_id' => $school_id,
                'interests' => $interests
            ]);

            header('Location: ../View/login.php?registration=success');
            exit();

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo "Erreur : Cette adresse email est déjà associée à un compte.";
            } else {
                echo "Une erreur technique est survenue : " . $e->getMessage();
            }
        }

    } else {
        echo "Erreur : Veuillez remplir tous les champs correctement (votre email n'est peut-être pas valide).";
    }
} else {
    header('Location: ../View/register.php');
    exit();
}