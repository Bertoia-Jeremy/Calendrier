<?php
/* Ici se situe la connexion à la base de données, si vous êtes en local sur window :
	$bdd = new PDO('mysql:host=127.0.0.1;dbname=BDD;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
Sur mac :
	$bdd = new PDO('mysql:host=127.0.0.1;dbname=BDD;charset=utf8', 'root', 'root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
 */

/* connexion bdd */
try { 
		$bdd = new PDO('mysql:host=127.0.0.1;dbname=BDD;charset=utf8', 'root', 'root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	}
catch(Exception $e){
    die('Erreur : '. $e->getMessage());
}
?>