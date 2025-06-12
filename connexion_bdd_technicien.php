<?php
// PHP --> MySQL
// Paramètres de la BdD (hôte de la BdD, nom de la BdD, numéro du port)
	$host='localhost';		// À MODIFIER ÉVENTUELLEMENT	
	$dbname='serre_supervision';				// À COMPLÉTER
	$port='';
	
// Si le script php s'exécute sur un serveur différent du serveur MySQL,
// il faut préciser, dans la variable $host, l'adresse IP du serveur MySQL.
// Il faut alors qu'un utilisateur (associé à la base de données) sur le serveur MySQL
// soit défini avec comme caractéristiques $login et $pass et un hôte étant 
// Tout hôte : % ou Saisir une valeur : Adresse IP du serveur APACHE 
	
// Définition du nom de la source de données (Data Source Name dsn)
	$dsn='mysql:host='.$host.';port='.$port.';dbname='.$dbname.';charset=utf8';

// Compte autorisé à se connecter à cette base de données
	$login='serre_technicien';			// À COMPLÉTER
	$pass='technicien123';			// À COMPLÉTER
	
//  Dans le fichier principal :
//  try
//  {
// 		$bdd=new PDO($dsn,$login,$pass,$pdo_options);
// 		...
//	}
//  catch(PDOException $e)
//	{
//		die('Erreur MySQL : '.$e->getMessage());
//	}


// Description des options PDO ----------------------------------------------------------
// PDO::ATTR_ERRMODE
// Le mode pour reporter les erreurs de PDO.
// PDO::ERRMODE_EXCEPTION
// Lance des PDOExceptions.

// PDO::ATTR_CASE
// Force les noms de colonnes à une casse particulière.
// PDO::CASE_NATURAL
// Laisse les noms de colonnes tels que retournés par le pilote de base de données.

// PDO::ATTR_ORACLE_NULLS
// Détermine si et comment null et les chaînes vides devraient être converties.
// PDO::NULL_EMPTY_STRING
// Les chaînes vides sont converties en null.

// Option à rajouter si l'on veut insérer des données à partir d'un fichier dans une base
// Voir l'exemple de requête générée dans HEIDI SQL
// PDO::MYSQL_ATTR_LOCAL_INFILE (int)	PDO::MYSQL_ATTR_LOCAL_INFILE=>1
// Active LOAD LOCAL INFILE.

// Option pour la commande fetch
// PDO::ATTR_DEFAULT_FETCH_MODE
// Récupère une ligne depuis un jeu de résultats associé à l'objet PDOStatement.
// Le paramètre mode détermine la façon dont PDO retourne la ligne.
// PDO::FETCH_ASSOC: retourne un tableau indexé par le nom de la colonne comme retourné dans le jeu de résultats
// PDO::FETCH_BOTH (défaut): retourne un tableau indexé par les noms de colonnes et aussi par les numéros de colonnes, commençant à l'index 0, comme retournés dans le jeu de résultats
// PDO::FETCH_NUM : retourne un tableau indexé par le numéro de la colonne comme elle est retourné dans votre jeu de résultat, commençant à 0