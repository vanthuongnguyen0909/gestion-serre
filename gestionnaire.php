<?php
// Démarrer la session
session_start();
// Vérification du rôle en tant que gestionnaire
if ($_SESSION['role'] !== 'gestionnaire') {
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
    // Activation du mode exception pour gérer les erreurs PDO
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Affichage de l'erreur et arrêt du script si la connexion échoue
    die("Erreur de connexion : " . $e->getMessage());
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Gestionnaire</title>
    <link rel="stylesheet" href="style1.css"> 
</head>
<body>
	<h1>Interface Gestionnaire</h1>
	<br>
	<?php
	echo "Utilisateur : ".$_SESSION['prenom']. " ".$_SESSION['nom'];
	?>
<!-- Menu de navigation -->
	<nav style="padding-top:14px">
		<ul>
			<li><a href="gestionnaire.php">Accueil</a></li>
			<li><a href="add_culture.php">Ajouter une période de culture</a></li>
            <li><a href="gerer_periode_culture.php">Gérer la période de culture</a></li>
			<li><a href="consultation.php">Consulter les données</a></li>
			<li><a href="deconnexion.php">Se déconnecter</a></li>
		</ul>
	</nav>
</body>
</html>

<h3>Liste des périodes de culture</h3>
<?php
// Requête pour récupérer les périodes de culture
try {
    $sql = "SELECT DATE_FORMAT(date_debut,'%d/%m/%Y') AS debut,DATE_FORMAT(date_fin,'%d/%m/%Y') AS fin, nom, description FROM periodes_culture  
            JOIN cultures  ON periodes_culture.culture_id = cultures.id";
    $result = $bdd->query($sql);

    if ($result->rowCount() > 0) {
        echo '<table border="1">';
        echo '<tr><th>Début</th><th>Fin</th><th>Nom</th><th>Description</th></tr>';
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
					<td>" . htmlspecialchars($row['debut']) . "</td>
                    <td>" . htmlspecialchars($row['fin']) . "</td>
                    <td>" . htmlspecialchars($row['nom']) . "</td>
                    <td>" . htmlspecialchars($row['description']) . "</td>
                    
                  </tr>";
        }
        echo '</table>';
    } else {
        echo "<p>Aucune période de culture disponible.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>Erreur SQL : " . $e->getMessage() . "</p>";
}
?>