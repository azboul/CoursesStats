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
	    
        <!--<link rel="stylesheet" type="text/css" media="all" href="http://assets.ubuntu.com/sites/guidelines/css/latest/ubuntu-styles.css"/>-->
		
        <!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

        <link rel="stylesheet" type="text/css" href="style.css" />
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
				


			
				
<h2>Historique <span><form action="refresh_Table.php" method="post" class="no-linebreak">
						<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon glyphicon-refresh" aria-hidden="true"></span></button>
					</form></span></h2>				
				
					<br />
					<br />					
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
					<!--<h2>Nouveau</h2>-->
					
<div role="tabpanel">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs nav-pills" role="tablist">
    <li role="presentation" class="active"><a href="#AjoutSortie" aria-controls="AjoutSortie" role="tab" data-toggle="tab">Sortie</a></li>
    <li role="presentation"><a href="#AjoutParcours" aria-controls="AjoutParcours" role="tab" data-toggle="tab">Parcours</a></li>
 </ul>

 	<br />
 
  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="AjoutSortie">
	
					<form action="record_Sortie.php" method="post" class="form-horizontal">
							<fieldset>
								<div class="form-group">
									<label for="sortieDate" class="col-sm-3 control-label">Date</label>
									<div class="col-sm-9"><input name="sortieDate" id="sortieDate" type="date" tabIndex="1" placeholder="JJ/MM/YYYY"></div>
								</div>
								<div class="form-group">
									<label for="sortieId" class="col-sm-3 control-label">Parcours</label>
									<div class="col-sm-9"><select name="sortieId" id="sortieId" tabIndex="2" class="form-control">
							
<?php
$nomParcoursQuery = $theDatabase->query('select Nom from Parcours');
while ($row = $nomParcoursQuery->fetchArray()) {
?>
										<option ><?php echo( htmlspecialchars($row[0]));?></option>
<?php	
}
$nomParcoursQuery->finalize();
?>								
									</select></div>
								</div>
								<div class="form-group">	
									<label for="sortieTime" class="col-sm-3 control-label">Temps</label>
									<div class="col-sm-9"><input name="sortieTime" id="sortieTime" type="time" tabIndex="3" placeholder="HH:MM:SS"></div>
								</div>
								<div class="form-group">
									<label for="sortieComment" class="col-sm-3 control-label">Note</label>
									<div class="col-sm-9"><textarea name="sortieComment" id="sortieComment" cols="20" rows="6" tabIndex="4" class="form-control"></textarea></div>
								</div>
								<p class="text-center"><input class="btn btn-primary" type="submit" value="Ajouter la sortie" tabindex="5"></p>
							</fieldset>
						</form>
						
	
	</div>
    <div role="tabpanel" class="tab-pane " id="AjoutParcours">
	
						<form action="record_Parcours.php" method="post" class="form-horizontal">
								<div class="form-group">
									<label for="parcoursNom" class="col-sm-3 control-label">Nom</label>
									<div class="col-sm-9"><input name="parcoursNom" id="parcoursNom" tabIndex="6"></div>
								</div>
								<div class="form-group">
									<label for="parcoursLieux" class="col-sm-3 control-label">Lieux</label>
									<div class="col-sm-9"><input name="parcoursLieux" id="parcoursLieux" tabIndex="7" autocomplete="on"></div>
								</div>
								<div class="form-group">
									<label for="parcoursDistance" class="col-sm-3 control-label">Distance</label>
									<div class="col-sm-9"><input name="parcoursDistance" id="parcoursDistance" tabIndex="8"></div>
								</div>
								<div class="form-group">
									<label for="parcoursComment" class="col-sm-3 control-label">Note</label>
									<div class="col-sm-9"><textarea name="parcoursComment" id="parcoursComment" cols="20" rows="6" tabIndex="9" class="form-control"></textarea></div>
								</div>
				
								<p class="text-center"><input class="btn btn-primary" type="submit" value="Ajouter le parcours" tabindex="10"></p>							
						</form>	
	
	</div>
  </div>

</div>					
  <!--					
					
					<div>
						
<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">Ajout d'une sortie						
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">						
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
  </div>
  

					  <div class="panel panel-default">
						<div class="panel-heading" role="tab" id="headingTwo">
						  <h4 class="panel-title">
							<a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Ajout d'un parcours						
							</a>
						  </h4>
						</div>
						<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
						  <div class="panel-body">						
						
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
-->						
				
					</div>					
						
					</div>
				</div>
			</div>

			<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
			<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
			<!-- Include all compiled plugins (below), or include individual files as needed -->
			<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
		</div>
    </body>
</html>
