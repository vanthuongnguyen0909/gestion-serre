<?php
session_start();
if ($_SESSION['role'] !== 'employe') {
    session_destroy();
    header("Location: index.html");
    exit();
}

require 'connexion_bdd_employe.php';

try {
    $bdd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $login, $pass);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $bdd->query("SELECT serre_id, temperature, humidite, dateheure FROM mesures_serre ORDER BY dateheure DESC LIMIT 20");
    $mesures_serre = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Données des Serres</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #4CAF50;
            padding: 10px;
        }

        .menu {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: flex-end;
        }

        .menu li {
            margin-left: 20px;
        }

        .menu li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .menu li a:hover {
            text-decoration: underline;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }
    </style>

    <script>
        function confirmerDeconnexion() {
            if (confirm("Voulez-vous vraiment vous déconnecter ?")) {
                window.location.href = "deconnexion.php";
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Données des Serres</h2>
        <table>
            <thead>
                <tr>
                    <th>Serre</th>
                    <th>Température (°C)</th>
                    <th>Humidité (%)</th>
                    <th>Date & Heure</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mesures_serre as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['serre_id']) ?></td>
                        <td><?= htmlspecialchars($m['temperature']) ?></td>
                        <td><?= htmlspecialchars($m['humidite']) ?></td>
                        <td><?= htmlspecialchars($m['dateheure']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>