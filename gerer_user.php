<?php
session_start();  // Démarrer la session		

// Vérification de l'authentification et du rôle de l'utilisateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technicien') {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);

    // Supprimer l'utilisateur
    $stmt = $bdd->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute([':id' => $deleteId]);

    // Redirection pour éviter le renvoi du formulaire
    header("Location: gerer_user.php?message=Utilisateur supprimé avec succès");
    exit();
}
// Récupération de la liste des utilisateurs
$sql = "SELECT id, nom, prenom, username, role FROM users";
$stmt = $bdd->query($sql);
$users = $stmt->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Modifier un utilisateur</title>
  <link rel="stylesheet" href="style1.css">
  <!-- Ajout de DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

  <script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            "paging": true,         // Active la pagination
            "searching": true,      // Active la barre de recherche
            "ordering": true,       // Active le tri des colonnes
            "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]], // Options pour afficher 5, 10, 25 ou 50 utilisateurs
            "pageLength": 10,       // Par défaut, afficher 10 utilisateurs
            "language": {
                "lengthMenu": "Afficher _MENU_ utilisateurs par page",
                "zeroRecords": "Aucun utilisateur trouvé",
                "info": "Affichage de l'élément _START_ à _END_ sur _TOTAL_ éléments",
                "infoEmpty": "Aucun utilisateur disponible",
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
	<h1>Interface Technicien</h1>
	<!-- Menu de navigation -->
    <nav style="padding-top:10px">
        <ul >
            <li><a href="technicien.php">Espace Technicien d'accueil</a></li>
        </ul>
    </nav>
    <br>
    <?php
    echo "Utilisateur : " . $_SESSION['prenom'] . " " . $_SESSION['nom'];
    ?>
  <h2>Gestion des utilisateurs</h2>
  <!-- Tableau des utilisateurs -->
  <table id="usersTable">
    <thead>
      <tr>
        <th>Nom</th>
        <th>Prenom</th>
        <th>Identifiant</th>
        <th>Role</th>
		<th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <td style="text-align:center;"><?php echo htmlspecialchars($user['nom']); ?></td>
          <td style="text-align:center;"><?php echo htmlspecialchars($user['prenom']); ?></td>
          <td style="text-align:center;"><?php echo htmlspecialchars($user['username']); ?></td>
          <td style="text-align:center;"><?php echo htmlspecialchars($user['role']); ?></td>
          <td style="text-align: center">
			<span style="display: inline-flex; align-items: center;">
				<!-- Bouton éditer avec l'ID utilisateur -->
				<a href="modifier_user.php?id=<?php echo $user['id']; ?> ">Éditer</a>

				<form method="POST" action="gerer_user.php" 
					  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');" 
					  style="display:inline;">
				  <input type="hidden" name="delete_id" value="<?php echo $user['id']; ?>">
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