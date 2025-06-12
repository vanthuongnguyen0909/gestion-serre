<?php
// Démarre la session
session_start();

// Supprime toutes les variables de session
session_unset();

// Vide complètement le tableau de session
$_SESSION=array();

// Détruit complètement la session
session_destroy();	

// Redirige l'utilisateur vers la page d'accueil 
header("Location: index.html");

// Arrête l'exécution du script pour éviter toute exécution de code supplémentaire
exit();
?>