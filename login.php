<?php
// Vérifier si les champs "Login" et "Password" ont été remplis
if ($_SERVER["REQUEST_METHOD"] !== "POST" || empty($_POST['Login']) || empty($_POST['Password'])) {
    header("Location: index.html?message=Verifier les champs saisis"); // Redirection vers la page de connexion
    exit();
}

// Inclusion du fichier de connexion à la base de données
require 'connexion_bdd_login.php';	


try {
	// Connexion à la base de données avec PDO
	$bdd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $login, $pass);
	// Activation du mode exception pour gérer les erreurs PDO
	$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// ======== Traitement du formulaire de connexion ========
	// Récupération des valeurs entrées par l'utilisateur
	$Login = $_POST['Login'];
	$Pass = $_POST['Password'];

	// Requête préparée pour récupérer l'utilisateur correspondant au login fourni
	$sql = "SELECT nom, prenom , password, role FROM users WHERE username = :username";
	$stmt = $bdd->prepare($sql);
	// Liaison du paramètre :username à la valeur entrée par l'utilisateur
	$stmt->bindParam(':username', $Login, PDO::PARAM_STR);
	// Exécution de la requête
	$stmt->execute();
	// Récupération du résultat sous forme de tableau associatif
	$user = $stmt->fetch(PDO::FETCH_ASSOC);

	// Vérification si un utilisateur a été trouvé
	if ($user) {
		// Vérification du mot de passe haché dans la base de données avec celui entré
		if (password_verify($Pass, $user['password'])) {
			// Démarrage de la session pour stocker les informations de l'utilisateur connecté
			session_start();
			
			// Connexion réussie : stockage des informations utilisateur dans la session
			$_SESSION['nom'] = $user['nom'];
			$_SESSION['prenom'] = $user['prenom'];
			$_SESSION['role'] = $user['role'];
			$_SESSION['entree_autorisee'] = true;

			// Redirection de l'utilisateur selon son rôle
			switch ($user['role']) {
				case 'gestionnaire':
					header("Location: gestionnaire.php");
					exit();
				case 'technicien':
					header("Location: technicien.php");
					exit();
				case 'employe':
					header("Location: employe.php");
					exit();
				default:
					header("Location: index.html");
					exit();
			}
		}
		else {
			// Mot de passe incorrect : affichage d'un message d'erreur			
			// Redirection vers la page de connexion
			header("Location: index.html?message=Echec d'authentification");
			exit();
		}
	} 
	else {
		// Aucun utilisateur trouvé avec ce login : affichage d'un message d'erreur
		// Redirection vers la page de connexion
		header("Location: index.html?message=Echec d'authentification");
		exit();
	}
} catch (PDOException $e) {
    // Affichage de l'erreur et arrêt du script si la connexion échoue
    die("Erreur de connexion : " . $e->getMessage());
}

?>