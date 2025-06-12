<?php
session_start();

// Vérification du rôle de l'utilisateur
if ($_SESSION['role'] !== 'employe') {
    session_unset();
    $_SESSION = array();
    session_destroy();
    header("Location: index.html?message=Accès non autorisé");
    exit();
}

// Inclusion de la connexion à la base de données
require 'connexion_bdd_employe.php';

try {
    $bdd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $login, $pass);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Technicien</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
    <h1>Interface Employé</h1>
    <br>
    <?php
    echo "Utilisateur : " . $_SESSION['prenom'] . " " . $_SESSION['nom'];
    ?>

    <!-- Menu de navigation -->
    <nav style="padding-top:15px">
        <ul>
            <li><a href="employe.php">Espace Employe d'accueil</a></li>
            <li><a href="consultation_employe.php">Consultation des données</a></li>
            <li><a href="deconnexion.php">Se déconnecter</a></li>
        </ul>
    </nav>
</body>
</html>