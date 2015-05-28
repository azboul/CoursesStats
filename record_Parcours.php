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

// Connection à la base de donnée
try {
	$theDatabase = new SQLite3('db/database.db');
}
catch(Exception $e) {
    DieWithMessage($e->getMessage());
}

// Nom du parcours
$theParcours = htmlspecialchars($_POST['parcoursNom']);
if( $IS_DEBUG)
    echo('<br> Parcours : ' . $theParcours);

// Vitesse
if( $IS_DEBUG)
    echo('<br> Vitesse : ' . $theSpeed);

// Lieux
$parcoursLieux = htmlspecialchars($_POST['parcoursLieux']);
if( $IS_DEBUG)
    echo('<br> Lieux : ' . $parcoursLieux);

// Distance
$parcoursDistance = htmlspecialchars($_POST['parcoursDistance']);

if( $IS_DEBUG)
    echo('<br> Distance : ' . $parcoursDistance);

// Commentaire
$parcoursComment = htmlspecialchars($_POST['parcoursComment']);
if( $IS_DEBUG)
    echo('<br> Commentaire : ' . $parcoursComment);

// Test validité du parcours
try
{
    $parcoursQuery = $theDatabase->query('SELECT * FROM  Parcours WHERE Nom="' . $theParcours  . '" ');
}
catch(Exception $e)
{
        echo( $e->getMessage());
}

if ( $parcoursQuery->fetchArray()[0] != null )
{
    DieWithMessage('Le parcours existe déja !');
}
$parcoursQuery->finalize();

// Validité de la distance
if( !is_numeric($parcoursDistance))
    DieWithMessage('La distance n\'est pas un nombre valide !');

// Insertion du message à l'aide d'une requête préparée
if( !$IS_DEBUG )
{
    try {
    	/*
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
    	*/

    	$query = 'INSERT INTO Parcours (Nom, Lieux, Distance, Note) VALUES("'.$theParcours.'", "'.$parcoursLieux.'", '.$parcoursDistance.', "'.$parcoursComment.'")';
    	$isOk = $theDatabase->exec($query);

    	if(!$isOk)
        {
            DieWithMessage('Erreur : ' . $theDatabase->lastErrorMsg());
    	}
         else
        {
    		echo 'No error';
    	}
    }
    catch(Exception $e) {
        DieWithMessage('Erreur : ' . $e->getMessage());
    }

    header('Location: refresh.php');
}

if( $IS_DEBUG )
    echo ('<br > Record ok !' . '<br /><a href="index.php">Retour au sommaire</a>');
?>
