<?php
// Démarrer la session
session_start();
// Vérification de l'authentification et du rôle de l'utilisateur
if ($_SESSION['role'] !== 'gestionnaire') {
	session_unset();
	$_SESSION=array();
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
	
    // Récupérer les périodes de culture avec le nom de la culture associée
    $stmt = $bdd->prepare("SELECT p.id, p.date_debut, p.date_fin, c.nom 
                           FROM periodes_culture p 
                           JOIN cultures c ON p.culture_id = c.id");
    $stmt->execute();
    $cultures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Affichage de l'erreur et arrêt du script si la connexion échoue
    die("Erreur de connexion : " . $e->getMessage());
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);

    // Supprimer la période de culture
    $stmt = $bdd->prepare("DELETE FROM periodes_culture WHERE id = :id");
    $stmt->execute([':id' => $deleteId]);

    // Redirection pour éviter le renvoi du formulaire
    header("Location: gerer_periode_culture.php?message=Culture supprimée avec succès");
    exit();
}

?>

<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Modifier une culture</title>
  <link rel="stylesheet" href="style1.css">
  <!-- Ajout de DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

  <script>
    $(document).ready(function() {
        $('#culturesTable').DataTable({
            "paging": true,         // Active la pagination
            "searching": true,      // Active la barre de recherche
            "ordering": true,       // Active le tri des colonnes
            "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]], // Options pour afficher 5, 10, 25 ou 50 utilisateurs
            "pageLength": 10,       // Par défaut, afficher 10 utilisateurs
            "language": {
                "lengthMenu": "Afficher _MENU_ cultures par page",
                "zeroRecords": "Aucun culture trouvé",
                "info": "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                "infoEmpty": "Aucun culture disponible",
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
	<h1>Interface Gestionnaire</h1>
    <br>
    <?php
    echo "Utilisateur : " . $_SESSION['prenom'] . " " . $_SESSION['nom'];
    ?>
    <nav style="padding-top:15px">
    <ul>
        <li><a href="gestionnaire.php">Espace Gestionnaire d'accueil</a></li>
    </ul>
    </nav> 
  <h2>Gestion des cultures</h2>
  <!-- Tableau des utilisateurs -->
  <table id="culturesTable">
    <thead>
      <tr>
        <th>Date_début</th>
        <th>Date_fin</th>
		<th>Nom_culture</th>
		<th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($cultures as $culture): ?>
		<tr>
		  <td style="text-align: center"><?php echo htmlspecialchars($culture['date_debut']); ?></td>
		  <td style="text-align: center"><?php echo htmlspecialchars($culture['date_fin']); ?></td>
		  <td style="text-align: center"><?php echo htmlspecialchars($culture['nom']); ?></td>
		  <td style="text-align: center">
		    <span style="display: inline-flex; align-items: center;">
				<!-- Bouton éditer avec l'ID utilisateur -->
				<a href="modifier_culture.php?id=<?php echo $culture['id']; ?> ">Éditer</a>

				<form method="POST" action="gerer_periode_culture.php" 
					  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');" 
					  style="display:inline;">
				  <input type="hidden" name="delete_id" value="<?php echo $culture['id']; ?>">
				   <button type="submit" 
					style="background: none; border: none; color: red; font: inherit; cursor: pointer; padding: 0; margin-left: 10px; text-decoration: none;"
					onmouseover="this.style.color='darkred'; this.style.textDecoration='underline';"
					onmouseout="this.style.color='red'; this.style.textDecoration='none';">
					Supprimer
				  </button>
				</form>
			</span>
		  </td>
		</tr>
	<?php endforeach; ?>

    </tbody>
  </table>

</body>
</html>


