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

// Nom du parcours
$theParcours = htmlspecialchars($_POST['parcoursNom']);
$parcoursQuery = $theDatabase->prepare('SELECT * FROM Parcours WHERE Nom = "' . $theParcours . '"');
$parcoursQuery->execute();
while ($row = $parcoursQuery->fetchArray()) {
    die('Le parcours existe déja !');
}
$parcoursQuery->finalize();

// Lieux
$parcoursLieux = htmlspecialchars($_POST['parcoursLieux']);

// Distance
$parcoursDistance = htmlspecialchars($_POST['parcoursDistance']);
if( !is_numeric($parcoursDistance))
	die('La distance n\'est pas un nombre valide !');
	
// Commentaire
$parcoursComment = htmlspecialchars($_POST['parcoursComment']);

// Insertion du message à l'aide d'une requête préparée
try {
	$finalReq = $theDatabase->prepare('INSERT INTO Parcours (Nom, Lieux, Distance, Note) VALUES(?, ?, ?, ?)');
	$finalReq->bindValue(1, $theParcours);
	$finalReq->bindValue(2, $parcoursLieux);
	$finalReq->bindValue(3, $parcoursDistance);
	$finalReq->bindValue(4, $parcoursComment);
	
	$isOk = $finalReq->execute();
	
	if(!$isOk) {
		die($theDatabase->lastErrorMsg());
	}
	$finalReq->finalize();
}
catch(Exception $e) {
	echo( $e->getMessage());
	die('Erreur : ' . $e->getMessage());
}

echo('requete enregistrée avec succès !');

header('Location: index_Courses.php');
?>
