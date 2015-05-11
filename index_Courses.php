<!DOCTYPE html>
<?php
//phpinfo(INFO_MODULES);
if(!class_exists('SQLite3'))
  die("SQLite 3 NOT supported.");

// Connection à la base de donnée
try {
	$theDatabase = new SQLite3('db/database.db');
}
catch(Exception $e) {
	die('Erreur : '.$e->getMessage());
}
?>

<html>
    <head>
        <title>Statistiques de course</title>
        <meta charset="utf-8" />
        <link rel="stylesheet" type="text/css" href="style.css" />
        <link rel="stylesheet" type="text/css" media="all" href="http://assets.ubuntu.com/sites/guidelines/css/latest/ubuntu-styles.css"/>
    </head>
    <body>
	    <!--<div class="row">
	    	<small>
<?php echo 'PHP (' . phpversion() . ') SQLite ('. SQLite3::version()[versionString] .')';    ?>
	    	</small>
	    </div>-->
		<div class="row row-enterprise no-border">
<?php
$genQuery = $theDatabase->query('select DateCreation, DistanceTot,time(TempsTot, "unixepoch") as TimeTot from General');
while ($row = $genQuery->fetchArray()) {
	$createDate = $row['DateCreation'];
	$sumDist = $row['DistanceTot'];
	$sumTime = $row['TimeTot'];	
	break;
}
$genQuery->finalize();
?>
			<div class="eight-col">
				<h1>Statistiques de course</h1>
				<p class="intro">(<?php echo $createDate ?>)</p>
			</div>
			<div class="four-col last-col">
				<div class="box box-highlight">
				<label><b>Distance totale : </b></label>
				<label><?php echo $sumDist ?> km</label><br />
				<label for=""><b>Temps total : </b></label>
				<label><?php echo $sumTime?></label><br />
				</div>
			</div>
		</div>
		<div class="row ">
			<div class="eight-col">
				<h2>Historique</h2>
				<form action="refresh_Table.php" method="post">
					<input type="submit" value="Rafraichir">
				</form>
				<table>
					<thead>
					<tr>
						<th scope="col" class="header-search">Date</th>
						<th scope="col" class="headerTable">Parcours</th>
						<th scope="col" class="headerTable">Temps</th>
						<th scope="col" class="headerTable">Vitesse</th>
					</tr>
					</thead>
					<tbody>
<?php
/**
 * Affichage de la table
 */
 
$allDBQuery = $theDatabase->query('select S.Date, S.Parcours, time(S.Temps, "unixepoch") as TempsFormat, S.Vitesse, S.Commentaire, P.Nom, P.Distance from Sorties S, Parcours P where S.Parcours = P.Nom');
while ($row = $allDBQuery->fetchArray()) {
	//var_dump($row);
	//$id = $row['Id'];
	$date = $row['Date'];
	$parcours = $row['Parcours'];
	$temps = $row['TempsFormat'];
	$vitesse = $row['Vitesse'];
	$comment = $row['Commentaire'];
	$distance = $row['Distance'];	
	?>
						<tr>
							<td><?php echo(htmlspecialchars($date));?>
<?php
if( $comment)	{
?>	
							<!--<img src="file://infos.png" alt="Centrify" />-->
							<br /><small><i><abbr title=<?php echo('"'.htmlspecialchars($comment).'"'); ?>>Infos</abbr></i></small>
<?php
} 
?>
							</td>
							<td><?php echo(htmlspecialchars($parcours) . ' <br /><small><i>' . htmlspecialchars($distance) . 'km</i></small>');?></td> 
							<td><?php echo(htmlspecialchars($temps));?></td>
							<td><?php echo(htmlspecialchars($vitesse));?> km/h</td>
						</tr>
<?php 
}
$allDBQuery->finalize();
?>
					</tbody>
				</table>
			</div>
		
			<div class="four-col last-col">
					<div class="box">
						<p><a href="#record_Sortie">Ajouter une sortie</a></p>
							<p><a href="#record_Parcours">Ajouter un parcours</a></p>
					</div>
					<div>
					<h2>Ajout d'une sortie</h2>
					<form action="record_Sortie.php" method="post"><a name="record_Sortie"></a>
						<fieldset>
							<p>
							<label for="sortieDate">Date : </label>
							<input name="sortieDate" id="sortieDate" type="date" tabIndex="1" placeholder="JJ/MM/YYYY">

							<label for="sortieId">Parcours : </label>
							<select name="sortieId" id="sortieId" tabIndex="2">
<?php
$nomParcoursQuery = $theDatabase->query('select Nom from Parcours');
while ($row = $nomParcoursQuery->fetchArray()) {
?>
										<option ><?php echo( htmlspecialchars($row[0]));?></option>
<?php	
}
$nomParcoursQuery->finalize();
?>								
							</select>
		
							<label for="sortieTime">Temps : </label>
							<input name="sortieTime" id="sortieTime" type="time" tabIndex="3" placeholder="HH:MM:SS">

							<label for="sortieComment">Commentaire : </label>
							<textarea name="sortieComment" id="sortieComment" cols="20" rows="6" tabIndex="4"></textarea>
							</p>
							
							<input type="submit" value="Ajouter la sortie" tabindex="5">
						</fieldset>
					</form>
				</div>
				<!--<div>
					<h2>Ajout d'un parcours</h2><a name="record_Parcours"></a>
					<form action="record_Parcours.php" method="post">
						<fieldset>
							<p>
							<label for="parcoursNom">Nom : </label>
							<input name="parcoursNom" id="parcoursNom" tabIndex="6">

							<label for="parcoursLieux">Lieux : </label>
							<input name="parcoursLieux" id="parcoursLieux" tabIndex="7" autocomplete="on">

							<label for="parcoursDistance">Distance : </label>
							<input name="parcoursDistance" id="parcoursDistance" tabIndex="8">

							<label for="parcoursComment">Commentaire : </label>
							<textarea name="parcoursComment" id="parcoursComment" cols="20" rows="6" tabIndex="9"></textarea>							
							</p>
							
							<input type="submit" value="Ajouter le parcours" tabindex="10">							
						</fieldset>
					</form>
				</div>-->
			</div>
		</div>		
    </body>
</html>
