<?php
session_start();

// Vérification de l'authentification
if ($_SESSION['role'] !== 'gestionnaire') {
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

// --- Ajout d'une période de culture ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_period_culture'])) {
    $culture_id = $_POST['culture_id'];  // CHANGÉ : correspond au nom du champ <select>
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    if (!empty($culture_id) && !empty($date_debut) && !empty($date_fin)) {
        try {
            // Vérifie si la culture existe
            $stmt = $bdd->prepare("SELECT nom FROM cultures WHERE id = :culture_id");
            $stmt->execute(['culture_id' => $culture_id]);
            $culture = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($culture) {
                $sql = "INSERT INTO periodes_culture (culture_id, date_debut, date_fin) 
                        VALUES (:culture_id, :date_debut, :date_fin)";
                $stmt = $bdd->prepare($sql);
                $stmt->bindParam(':culture_id', $culture_id, PDO::PARAM_INT);
                $stmt->bindParam(':date_debut', $date_debut, PDO::PARAM_STR);
                $stmt->bindParam(':date_fin', $date_fin, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    echo "✅ Période de culture ajoutée avec succès !";
                    // Redirection pour rafraîchir la liste
                    header("Location: gestionnaire.php");
                    exit();
                } else {
                    echo "❌ Erreur lors de l'ajout de la période.";
                }
            } else {
                echo "❌ Culture non trouvée.";
            }
        } catch (PDOException $e) {
            echo "Erreur SQL : " . $e->getMessage();
        }
    } else {
        echo "❗ Tous les champs sont obligatoires.";
    }
}

// --- Ajout d'une nouvelle culture ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_culture'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];

    if (!empty($nom) && !empty($description)) {
        try {
            $stmt = $bdd->prepare("INSERT INTO cultures (nom, description) VALUES (:nom, :description)");
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo "✅ Culture ajoutée avec succès !";
            } else {
                echo "❌ Erreur lors de l'ajout de la culture.";
            }
        } catch (PDOException $e) {
            echo "Erreur SQL : " . $e->getMessage();
        }
    } else {
        echo "❗ Tous les champs sont obligatoires.";
    }
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
    <nav style="padding-top:15px">
        <ul>
            <li><a href="gestionnaire.php">Espace Gestionnaire d'accueil</a></li>
        </ul>
    </nav> 
<h2>Ajouter une période de culture</h2>

<!-- Formulaire pour ajouter une nouvelle période de culture -->
<form action="add_culture.php" method="POST">
    <label for="culture_id">Choisir une culture :</label><br>
    <select name="culture_id" required>
        <option value="">-- Sélectionnez une culture --</option>
        <?php
        // Charger la liste des cultures depuis la base de données
        try {
            $cultures = $bdd->query("SELECT id, nom FROM cultures");
            while ($culture = $cultures->fetch(PDO::FETCH_ASSOC)) {
                echo '<option value="' . $culture['id'] . '">' . htmlspecialchars($culture['nom']) . '</option>';
            }
        } catch (PDOException $e) {
            echo '<option value="">Erreur de chargement</option>';
        }
        ?>
    </select><br><br>

    <label for="date_debut">Date de début :</label><br>
    <input type="date" name="date_debut" required><br><br>

    <label for="date_fin">Date de fin :</label><br>
    <input type="date" name="date_fin" required><br><br>

    <button type="submit" name="add_period_culture">Ajouter une période de culture</button>
</form>

<h3>Ajouter une nouvelle culture</h3>
<!-- Formulaire pour ajouter une nouvelle culture -->
<form action="add_culture.php" method="POST">
    <label for="nom">Nom de la culture :</label><br>
    <input type="text" name="nom" required><br><br>

    <label for="description">Description :</label><br>
    <textarea name="description" required></textarea><br><br>

    <button type="submit" name="add_culture">Ajouter une culture</button>
</form>
</body>
</html>