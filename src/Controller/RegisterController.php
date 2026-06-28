<?php

require_once '../Model/Database.php';
require_once '../Model/LogModel.php';

$logModel = new LogModel();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/register.php');
    exit();
}

$firstname = htmlspecialchars(trim($_POST['firstname']));
$lastname = htmlspecialchars(trim($_POST['lastname']));
$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$age = intval($_POST['age']);
$password = $_POST['password'];
$school_id = !empty($_POST['school_id']) ? intval($_POST['school_id']) : null;
$interests = htmlspecialchars(trim($_POST['interests']));
$instagram = htmlspecialchars(trim($_POST['instagram']));

if (!$firstname || !$lastname || !$email || !$age || !$password || !$school_id) {
    die("Champs invalides");
}

try {
    $db = Database::getConnection();

    $stmt = $db->prepare("
        INSERT INTO users 
        (firstname, lastname, email, password, age, school_id, interests, instagram, role, status)
        VALUES 
        (:firstname, :lastname, :email, :password, :age, :school_id, :interests, :instagram, 'student', 'active')
    ");

    $stmt->execute([
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_BCRYPT),
        'age' => $age,
        'school_id' => $school_id,
        'interests' => $interests,
        'instagram' => $instagram
    ]);

    $logModel->add(
        'INSCRIPTION',
        "Nouvel étudiant inscrit : $firstname $lastname ($email)"
    );

    header('Location: ../View/login.php?registration=success');
    exit();

} catch (PDOException $e) {

    if ($e->getCode() == 23000) {
        die("Email déjà utilisé");
    }

    $logModel->add('SYSTEM_ERROR', $e->getMessage());
    die("Erreur technique");
}