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
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    
        <link rel="stylesheet" type="text/css" href="style.css" />
        <!--<link rel="stylesheet" type="text/css" media="all" href="http://assets.ubuntu.com/sites/guidelines/css/latest/ubuntu-styles.css"/>-->
		
        <!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
		</head>
    <body>
    	<div class="container">
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

<!-- Entête
================================================== -->
			<div class="page-header">
				<div class="row">
					<h1>Statistiques de course</h1>
					<p>(<?php echo $createDate ?>)</p>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<ul class="list-group">
						<li class="list-group-item"><b>Distance totale : </b><?php echo $sumDist ?> km</li>
						<li class="list-group-item"><b>Temps total : </b><?php echo $sumTime?></li>
					</ul>
				</div>
				<div class="col-md-4"></div>
			</div>
			
			<div class="row">
<!-- Historique
================================================== -->			
				<div class="col-md-8">
					<h2 class="sub-header">Historique</h2>
					<form action="refresh_Table.php" method="post">
						<input type="submit" value="Rafraichir">
					</form>
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th scope="col">Date</th>
									<th scope="col">Parcours</th>
									<th scope="col">Temps</th>
									<th scope="col">Vitesse</th>
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
									<!--<br />-->
									<small><i>
									<abbr title=<?php echo('"'.htmlspecialchars($comment).'"'); ?>><span class="label label-info">Info</span></abbr>
									</i></small>
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
				</div>
		
<!-- Formulaires
================================================== -->

				<div class="col-md-4">
					<div>
						
<div class="accordion" id="accordion2">
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">						
						
						<h2>Ajout d'une sortie</h2>
						
      </a>
    </div>
    <div id="collapseOne" class="accordion-body collapse in">
      <div class="accordion-inner">						
						
						<form action="record_Sortie.php" method="post"><a name="record_Sortie"></a>
							<fieldset>
								<div class="form-group">
									<label for="sortieDate">Date</label>
									<input name="sortieDate" id="sortieDate" type="date" tabIndex="1" placeholder="JJ/MM/YYYY">
								</div>
								<div class="form-group">
									<label for="sortieId">Parcours</label>
									<select name="sortieId" id="sortieId" tabIndex="2" class="form-control">
							
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
								</div>
								<div class="form-group">	
									<label for="sortieTime">Temps</label>
									<input name="sortieTime" id="sortieTime" type="time" tabIndex="3" placeholder="HH:MM:SS">
								</div>
								<div class="form-group">
									<label for="sortieComment">Commentaire</label>
									<textarea name="sortieComment" id="sortieComment" cols="20" rows="6" tabIndex="4" class="form-control"></textarea>
								</div>
								<input class="btn btn-primary" type="submit" value="Ajouter la sortie" tabindex="5">
							</fieldset>
						</form>
						
      </div>
    </div>
  </div>
  <div class="accordion-group">
    <div class="accordion-heading">
      <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">						
						
						<h2>Ajout d'un parcours</h2><a name="record_Parcours"></a>
						
      </a>
    </div>
    <div id="collapseTwo" class="accordion-body collapse">
      <div class="accordion-inner">						
						
						<form action="record_Parcours.php" method="post">
							<fieldset>
								<div class="form-group">
									<label for="parcoursNom">Nom</label>
									<input name="parcoursNom" id="parcoursNom" tabIndex="6">
								</div>
								<div class="form-group">
									<label for="parcoursLieux">Lieux</label>
									<input name="parcoursLieux" id="parcoursLieux" tabIndex="7" autocomplete="on">
								</div>
								<div class="form-group">
									<label for="parcoursDistance">Distance</label>
									<input name="parcoursDistance" id="parcoursDistance" tabIndex="8">
								</div>
								<div class="form-group">
									<label for="parcoursComment">Commentaire</label>
									<textarea name="parcoursComment" id="parcoursComment" cols="20" rows="6" tabIndex="9" class="form-control"></textarea>
								</div>
				
								<input class="btn btn-primary" type="submit" value="Ajouter le parcours" tabindex="10">							
							</fieldset>
						</form>
						
      </div>
    </div>
  </div>
</div>						
						
						
					</div>
				</div>
			</div>

			<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
			<!-- Include all compiled plugins (below), or include individual files as needed -->
			<script src="js/bootstrap.min.js"></script>		
		</div>
    </body>
</html>
