<?php
// Démarrage de la session
session_start();

// Vérification de l'authentification et du rôle de l'utilisateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technicien'){
    session_unset();
    $_SESSION = array();
    session_destroy();
    header("Location: index.html?message=Accès non autorisé");
    exit();
}

// Inclusion du fichier de connexion à la base de données
require 'connexion_bdd_technicien.php';
try {
    // Connexion à la base de données avec PDO
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
    <h1>Interface Technicien</h1>
    <br>
    <?php
    echo "Utilisateur : " . $_SESSION['prenom'] . " " . $_SESSION['nom'];
    ?>

    <!-- Menu de navigation -->
    <nav style="padding-top:15px">
        <ul>
            <li><a href="technicien.php">Espace Technicien d'accueil</a></li>
            <li><a href="add_user.php">Ajouter un utilisateur</a></li>
            <li><a href="gerer_user.php">Gérer les utilisateurs</a></li>
            <li><a href="deconnexion.php">Se déconnecter</a></li>
        </ul>
    </nav>
</body>
</html>

<h3>Liste des utilisateurs</h3>
<?php
$sql = "SELECT nom, prenom, username, role FROM users";
$result = $bdd->query($sql);

// Vérification si des utilisateurs existent
if ($result->rowCount() > 0) {
    echo "<table border='1'>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Identifiant</th>
                <th>Rôle</th>
            </tr>";

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['nom']) . "</td>
                <td>" . htmlspecialchars($row['prenom']) . "</td>
                <td>" . htmlspecialchars($row['username']) . "</td>
                <td>" . htmlspecialchars($row['role']) . "</td>
              </tr>";	
    }
    echo "</table>";
} else {
    echo "Aucun utilisateur trouvé.";
}
?>