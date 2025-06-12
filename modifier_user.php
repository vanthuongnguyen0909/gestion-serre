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

// Vérifier si un ID utilisateur est passé en paramètre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Aucun utilisateur sélectionné.");
}

$id = intval($_GET['id']); // Sécurisation de l'ID

// Récupération des informations de l'utilisateur
$stmt = $bdd->prepare("SELECT id, nom, prenom, username, role FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur existe
if (!$user) {
    die("Utilisateur introuvable.");
}

// Traitement de la mise à jour si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
    $new_password = $_POST['new_password'];
    $new_role = $_POST['role'];

    // Hachage du mot de passe avant de le stocker
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Mise à jour du mot de passe et du rôle dans la base de données
    $update_stmt = $bdd->prepare("UPDATE users SET password = :password, role = :role WHERE id = :id");
    $update_stmt->execute([
        ':password' => $hashed_password,
        ':role' => $new_role,
        ':id' => $id
    ]);

    // Redirection après mise à jour
    header("Location: gerer_user.php?message=Utilisateur mis à jour avec succès");
    exit();
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Modifier un utilisateur</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
	<h1>Interface Technicien</h1>
	<!-- Menu de navigation -->
    <nav style="padding:10px">
        <ul>
            <li><a href="gerer_user.php">Gérer les utilisateurs</a></li>
        </ul>
    </nav>
    <br>
    <?php
    echo "Utilisateur : " . $_SESSION['prenom'] . " " . $_SESSION['nom'];
    ?>
    <h2>Modifier l'utilisateur</h2>
    <form action="" method="POST">
        <label for="nom">Nom</label>
        <input type="text" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" readonly><br>

        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" readonly><br>

        <label for="username">Identifiant</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly><br>

        <label for="role">Rôle</label>
        <select name="role">
            <option value="gestionnaire" <?php if ($user['role'] === 'gestionnaire') echo 'selected'; ?>>Gestionnaire</option>
			<option value="technicien" <?php if ($user['role'] === 'technicien') echo 'selected'; ?>>Technicien</option>
            <option value="employe" <?php if ($user['role'] === 'employe') echo 'selected'; ?>>Employé</option>
        </select><br><br>

        <label for="new_password">Nouveau mot de passe</label>
        <input type="password" name="new_password" required><br>

        <input type="submit" value="Mettre à jour">
    </form>
</body>
</html>
