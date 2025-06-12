<?php
// Démarrer la session
session_start();

// Vérification du rôle en tant que gestionnaire
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'gestionnaire') {
    session_unset();
    $_SESSION = [];
    session_destroy();
    header("Location: index.html?message=Accès non autorisé");
    exit();
}

require 'connexion_bdd_gestionnaire.php';

try {
    // Connexion à la base de données avec PDO
    $bdd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $login, $pass);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Construire la requête SQL avec filtres
    $sql = "SELECT serre_id, temperature, humidite, dateheure FROM mesures_serre WHERE 1=1";
    $params = [];

    if (!empty($_GET['date_debut'])) {
        $sql .= " AND dateheure >= :date_debut";
        $params[':date_debut'] = $_GET['date_debut'] . " 00:00:00";
    }

    if (!empty($_GET['date_fin'])) {
        $sql .= " AND dateheure <= :date_fin";
        $params[':date_fin'] = $_GET['date_fin'] . " 23:59:59";
    }

    if (!empty($_GET['serre_id'])) {
        $sql .= " AND serre_id = :serre_id";
        $params[':serre_id'] = $_GET['serre_id'];
    }

    $stmt = $bdd->prepare($sql); // ✅ bonne requête avec filtres
    $stmt->execute($params);
    $mesures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat de requêtes</title>
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
	<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "paging": true,         // Active la pagination
            "searching": true,      // Active la barre de recherche
            "ordering": true,       // Active le tri des colonnes
            "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]], // Options pour afficher 5, 10, 25 ou 50 les donnees
            "pageLength": 10,       // Par défaut, afficher 10 les donnees
            "language": {
                "lengthMenu": "Afficher _MENU_  par page",
                "zeroRecords": "Aucune donnee trouvée",
                "info": "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                "infoEmpty": "Aucune donnee disponible",
                "infoFiltered": "(filtré à partir de _MAX_ éléments au total)",
                "search": "Rechercher :",
                "paginate": {
                    "first": "Premier",
                    "last": "Dernier",
                    "next": "Suivant",
                    "previous": "Précédent"
                }
            }
        });
    });
  </script>
</head>
<body>
<h1>Interface gestionnaire</h1>
    <br>
    <?php
    echo "Utilisateur : " . $_SESSION['prenom'] . " " . $_SESSION['nom'];
    ?>
    <nav style="padding-top:10px">
        <ul>
            <li><a href="gestionnaire.php">Accueil</a></li>
        </ul>
    </nav> 
<h2>Consulter les donnees</h2>
<!-- Formulaire de filtre -->
<form method="GET" style="text-align:center; margin-bottom: 20px;">
    <label for="date_debut">Date début :</label>
    <input type="date" name="date_debut" id="date_debut" value="<?= htmlspecialchars($_GET['date_debut'] ?? '') ?>">

    <label for="date_fin">Date fin :</label>
    <input type="date" name="date_fin" id="date_fin" value="<?= htmlspecialchars($_GET['date_fin'] ?? '') ?>">

    <label for="serre_id">ID Serre :</label>
    <input type="number" name="serre_id" id="serre_id" value="<?= htmlspecialchars($_GET['serres_id'] ?? '') ?>">

    <input type="submit" value="Filtrer">
</form>
<table id="usersTable" style="background:#FFF" align="center" width="60%" border="4" cellpadding="4" rules="all">
    <caption align="top"><strong>Résultat de la requête</strong><br><br></caption>
    <thead>
        <tr>
            <th>Id Serre</th>
            <th>Température</th>
            <th>Humidité</th>
            <th>Date/Heure</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($mesures as $data): ?>
            <tr>
                <td><?= htmlspecialchars($data['serre_id']) ?></td>
                <td><?= htmlspecialchars($data['temperature']) ?> °C</td>
                <td><?= htmlspecialchars($data['humidite']) ?> %</td>
                <td><?= htmlspecialchars($data['dateheure']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>