<?php

function getArgType($arg)
{
    switch (gettype($arg))
    {
        case 'double': return SQLITE3_FLOAT;
        case 'integer': return SQLITE3_INTEGER;
        case 'boolean': return SQLITE3_INTEGER;
        case 'NULL': return SQLITE3_NULL;
        case 'string': return SQLITE3_TEXT;
        default:
            throw new \InvalidArgumentException('Argument is of invalid type '.gettype($arg));
    }
}

// Effectue la requete d'ajout dans la base
// Et redirige sur index_Courses.hp

// Connection à la base de donnée
try {
	$theDatabase = new SQLite3('db/database.db');
}
catch(Exception $e) {
	die('Erreur : '.$e->getMessage());
}

// Date
$theDate = htmlspecialchars($_POST['sortieDate']);
//TODO : vérifier la validité de la date
//echo($theDate);

// Nom du parcours
$theParcours = htmlspecialchars($_POST['sortieId']);
$distanceQuery = $theDatabase->query('SELECT Distance FROM Parcours WHERE Nom = "' . $theParcours . '"');
$theDistance=0.0;
while ($row = $distanceQuery->fetchArray()) {
    $theDistance = $row[0];
    break;
}
$distanceQuery->finalize();

// Temps
$theTime = htmlspecialchars($_POST['sortieTime']);
list( $hour, $min, $sec) = explode(":", $theTime);

//TODO : vérifier la validité de la durée
$timeInSecond = 3600*$hour+60*$min+$sec;
if ($timeInSecond <= 0.0) {
	die('Temps de parcours non valide : ');
}

// Commentaire
$theComment = htmlspecialchars($_POST['sortieComment']);

// Calcul de la Vitesse
$theSpeed = round($theDistance*3600/($timeInSecond), 2);

// Insertion du message à l'aide d'une requête préparée
try {

	/*
	$finalReq = $theDatabase->prepare('INSERT INTO Sorties (Date, Parcours, Temps, Vitesse, Commentaire) VALUES(?, ?, ?, ?, ?)');
	$finalReq->bindValue(1, '"'.$theDate.'"', 		getArgType(theDate));
	$finalReq->bindValue(2, '"'.$theParcours.'"',	getArgType(theParcours));
	$finalReq->bindValue(3, $timeInSecond,	getArgType(timeInSecond));
	$finalReq->bindValue(4, $theSpeed,		getArgType(theSpeed));
	$finalReq->bindValue(5, '"'.$theComment.'"',	getArgType(theComment));	
	
	$isOk = $finalReq->exec();
	*/
	
		
	$query = 'INSERT INTO Sorties (Date, Parcours, Temps, Vitesse, Commentaire) VALUES('.$theDate.', '.$theParcours.', '.$timeInSecond.', '.$theSpeed.', '.$theComment.')';
	$isOk = $theDatabase->exec('INSERT INTO Sorties (Date, Parcours, Temps, Vitesse, Commentaire) VALUES("'.$theDate.'", "'.$theParcours.'", '.$timeInSecond.', '.$theSpeed.', "'.$theComment.'")');
	
	if(!$isOk) {
		die('Erreur : ' . $theDatabase->lastErrorMsg());
	} else {
		echo 'No error';
	}
	
	//$finalReq->finalize();
}
catch(Exception $e) {
	die('Erreur : ' . $e->getMessage());
}

//echo('requete enregistrée avec succès !');
//header('Location: index_Courses.php');
header('Location: refresh_Table.php');
//echo ('Record ok !' . '<br /><a href="index_Courses.php">Retour au sommaire</a>');
?>
