<?php
/*
 * Effectue la requete d'ajout dans la base
 * Et redirige sur l'acceuil
 */

$IS_DEBUG=false;

function DieWithMessage($msg)
{
    echo('<br>'.$msg.'<br>');
    die('<a href="index.php">Retour au sommaire</a><br>');
}

// Connection à la base de donnée
try {
	$theDatabase = new SQLite3('db/database.db');

	// MAJ des vitesses dans Sorties
	$distCalc = '3600 *( Parcours.Distance/Sorties.Temps)';
	if( $IS_DEBUG)
	    echo('<br> Distance Tot : ' .  $distCalc);

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
	DieWithMessage('Erreur : '.$e->getMessage());
}

header('Location: index.php');
if( $IS_DEBUG )
    echo ('<br > Refresh ok !' . '<br /><a href="index.php">Retour au sommaire</a>');
?>
