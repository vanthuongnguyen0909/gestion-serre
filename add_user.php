<?php
// Démarrage de la session
session_start();
// Vérification de l'authentification et du rôle de l'utilisateur

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technicien') {
	session_unset();
	$_SESSION=array();
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
// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_users'])) {
    $nom = trim($_POST['nom']);  // Récupérer la valeur du champ "nom"
    $prenom = trim($_POST['prenom']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (!empty($nom) && !empty($prenom) && !empty($username) && !empty($password) && !empty($role)) {
        // Vérification si l'identifiant existe déjà
        $check = $bdd->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $check->execute([':username' => $username]);
        $exists = $check->fetchColumn();

        if ($exists > 0) {
            $error = "Cet identifiant existe déjà. Veuillez en choisir un autre.";
        } else {
            // Hachage du mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Utilisation correcte de PDO avec requête préparée
            $stmt = $bdd->prepare("INSERT INTO users (Nom, Prenom, username, password, role) VALUES (:nom, :prenom, :username, :password, :role)");
            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':username' => $username,
                ':password' => $hashed_password,
                ':role' => $role
            ]);

            // Redirection pour rafraîchir la liste
            header("Location: technicien.php");
            exit();
        }
    } else {
        $error = "Tous les champs sont obligatoires.";
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Ajouter un utilisateur</title>
  <link rel="stylesheet" href="style1.css">
</head>
<body>
<h1>Interface Technicien</h1>
    <br>
    <?php
    echo "Utilisateur : " . $_SESSION['prenom'] . " " . $_SESSION['nom'];
    ?>
<h2>Ajouter un utilisateur</h2>

<!-- Formulaire pour ajouter un employé -->
<form action="add_user.php" method="POST">
	<label for="nom">Nom :</label><br>
    <input type="text" name="nom" required><br><br>
	
	<label for="prenom">Prenom :</label><br>
    <input type="text" name="prenom" required><br><br>
	
    <label for="username">Identifiant :</label><br>
    <input type="text" name="username" required><br><br>

    <label for="password">Mot de passe :</label><br>
    <input type="password" name="password" required><br><br>

    <label for="role">Rôle :</label><br>
    <select name="role" required>
        <option value="gestionnaire">Gestionnaire</option>
		<option value="technicien">Technicien</option>
        <option value="employe">Employé</option>
    </select><br><br>

    <button type="submit" name="add_users">Ajouter un utilisateur</button>
<?php if (isset($error)) : ?>
    <p style="color: red; font-weight: bold;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
</form>
</body>
</html>