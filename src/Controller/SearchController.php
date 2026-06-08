<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../View/login.php');
    exit();
}

// Récupération mot-clé (la requête 'q') envoyé en GET
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';

$results = [];

if (!empty($searchQuery)) {
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=student_link;charset=utf8', 'root', 'root');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT u.id, u.firstname, u.lastname, u.interests, s.name as school_name 
                FROM users u 
                LEFT JOIN schools s ON u.school_id = s.id 
                WHERE (u.firstname LIKE :q 
                   OR u.lastname LIKE :q 
                   OR u.interests LIKE :q 
                   OR s.name LIKE :q)
                AND u.id != :my_id 
                AND u.status = 'active'
                ORDER BY u.lastname ASC";

        $stmt = $bdd->prepare($sql);
        $stmt->execute([
            'q' => '%' . $searchQuery . '%', // Le % permet de trouver le mot n'importe où dans la chaîne
            'my_id' => $_SESSION['user_id']
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        die('Erreur de recherche : ' . $e->getMessage());
    }
}