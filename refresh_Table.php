<?php

// Connection à la base de donnée
try {
	$theDatabase = new SQLite3('db/database.db');

	// MAJ des vitesses dans Sorties
	$distCalc = '3600 *( Parcours.Distance/Sorties.Temps)';	
	$theQuery = $theDatabase->query('update Sorties set Vitesse = ( select round( '.$distCalc.', 2)	from Parcours where Parcours.Nom = Sorties.Parcours)');
	$theQuery->finalize();
		
	// MAJ des éléments dans General
	$queryStr = ' update General'.
				' set TempsTot ='.
				' (select sum(Temps) from Sorties ),'.
				' DistanceTot ='.
				' (select sum(Distance) from Sorties, Parcours where Sorties.Parcours = Parcours.Nom)';
	
	$theQuery = $theDatabase->query($queryStr);
	$theQuery->finalize();
}	
catch(Exception $e) {
	die('Erreur : '.$e->getMessage());
}

header('Location: index_Courses.php');
//echo ('Refresh ok !' . '<br /><a href='index_Courses.php'>Retour au sommaire</a>');
?>
