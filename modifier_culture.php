<?php
session_start();  // Démarrer la session

// Vérification du rôle
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gestionnaire') {
    session_unset();
    $_SESSION = array();
    session_destroy();
    header("Location: index.html?message=Accès non autorisé");
    exit();
}

require 'connexion_bdd_gestionnaire.php';

try {
    $bdd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $login, $pass);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérification de l’ID de la période de culture
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Aucune période sélectionnée.");
}

$id_periode = intval($_GET['id']);

// Récupérer la période de culture et la culture associée
$stmt = $bdd->prepare("
    SELECT pc.date_debut, pc.date_fin, c.nom, c.description
    FROM periodes_culture pc
    JOIN cultures c ON pc.culture_id = c.id
    WHERE pc.id = :id
");
$stmt->execute([':id' => $id_periode]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Période ou culture introuvable.");
}

// Mise à jour uniquement des dates
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    $update = $bdd->prepare("UPDATE periodes_culture SET date_debut = :date_debut, date_fin = :date_fin WHERE id = :id");
    $update->execute([
        ':date_debut' => $date_debut,
        ':date_fin' => $date_fin,
        ':id' => $id_periode
    ]);
    
	// Redirection après modification
    header("Location: gerer_periode_culture.php?message=Période modifiée avec succès");
    exit();
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Modifier période de culture</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
    <h1>Interface Gestionnaire</h1>
    <p>Utilisateur : <?php echo htmlspecialchars($_SESSION['prenom'] . " " . $_SESSION['nom']); ?></p>
    <!doctype html>
    <nav style="padding-top:10px">
        <ul>
            <li><a href="gerer_periode_culture.php">Gérer les cultures</a></li>
        </ul>
    </nav> 
    <h2>Modifier la période de la culture</h2>
    <p><strong>Nom :</strong> <?php echo htmlspecialchars($data['nom']); ?></p>
    <p><strong>Description :</strong> <?php echo nl2br(htmlspecialchars($data['description'])); ?></p>

    <form method="POST">
        <label for="date_debut">Date de début</label>
        <input type="date" name="date_debut" value="<?php echo htmlspecialchars($data['date_debut']); ?>" required><br>

        <label for="date_fin">Date de fin</label>
        <input type="date" name="date_fin" value="<?php echo htmlspecialchars($data['date_fin']); ?>" required><br><br>

        <input type="submit" value="Mettre à jour les dates">
    </form>
</body>
</html>


