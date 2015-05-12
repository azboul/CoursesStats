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
	    		
        <!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

        <link rel="stylesheet" type="text/css" href="style.css" />
	</head>
    <body>
    	<div class="container ">

<!-- Entête
================================================== -->
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

			<div class="row page-header ">
				<h1>Statistiques de course 
					<small><?php echo $createDate ?></small>
				</h1>
			</div>
		
			<div class="row">

<!-- Historique
================================================== -->			
				<div class="col-md-8">
					<div class="row">
						<h2>Historique 
							<span>
								<form action="refresh_Table.php" method="post" class="no-linebreak">
									<button type="submit" class="btn btn-default">
										<span class="glyphicon glyphicon glyphicon-refresh" aria-hidden="true"></span>
									</button>
								</form>
							</span>
						</h2>				
				
						<div class="table-responsive">
							<!--<table class="table table-striped table-hover">-->
							<table class="table">							
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
<?php if( $comment)	{ ?>	
											<small><i>
												<abbr title=<?php echo('"'.htmlspecialchars($comment).'"'); ?>><span class="label label-info">Info</span></abbr>
											</i></small>
<?php } ?>
										</td>
										<td>
											<?php echo(htmlspecialchars($parcours));?><br />
											<small><i>
											<?php echo(htmlspecialchars($distance));?> km
											</i></small>
										</td> 
										<td><?php echo(htmlspecialchars($temps));?></td>
										<td><?php echo(htmlspecialchars($vitesse));?> km/h</td>
									</tr>
<?php } $allDBQuery->finalize();?>
								</tbody>
							</table>
						</div>
					</div>
				</div>	<!--col-md-8-->
	
				<div class="col-md-4">
<!-- Résumé
================================================== -->
<?php
$sumDist = $sumDist.'km';
list( $h, $m, $s) = explode(":", $sumTime);
$sumTime = $h.'h '.$m.'min '.$s.'s';
?>
					<div class="btn-warning">
						<dl class="dl-horizontal">
							<dt>Distance totale</dt>
							<dd><?php echo $sumDist ?></dd>
							<dt>Temps total</dt>
							<dd><?php echo $sumTime ?></dd>					  
						</dl>
					</div>

<!-- Formulaires
================================================== -->				
					<div class="panel panel-default">
						<div class="panel-body">
							<div role="tabpanel" >
								<ul class="nav nav-pills " role="tablist">
									<li role="presentation" class="active"><a href="#AjoutSortie" aria-controls="AjoutSortie" role="tab" data-toggle="tab">Sortie</a></li>
									<li role="presentation"><a href="#AjoutParcours" aria-controls="AjoutParcours" role="tab" data-toggle="tab">Parcours</a></li>
								</ul>
							 	<br />
								<div class="tab-content ">
									<div role="tabpanel" class="tab-pane active" id="AjoutSortie">
										<form action="record_Sortie.php" method="post" class="form-horizontal">
											<fieldset>
												<div class="form-group">
													<label for="sortieDate" class="col-sm-3 control-label">Date</label>
													<div class="col-sm-9"><input name="sortieDate" id="sortieDate" type="date" tabIndex="1" placeholder="JJ/MM/YYYY"></div>
												</div>
												<div class="form-group">
													<label for="sortieId" class="col-sm-3 control-label">Parcours</label>
													<div class="col-sm-9">
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
												<div class="col-sm-9">
													<input name="parcoursNom" id="parcoursNom" tabIndex="6">
												</div>
											</div>
											<div class="form-group">
												<label for="parcoursLieux" class="col-sm-3 control-label">Lieux</label>
												<div class="col-sm-9">
													<input name="parcoursLieux" id="parcoursLieux" tabIndex="7" autocomplete="on">
												</div>
											</div>
											<div class="form-group">
												<label for="parcoursDistance" class="col-sm-3 control-label">Distance</label>
												<div class="col-sm-9">
													<input name="parcoursDistance" id="parcoursDistance" tabIndex="8">
												</div>
											</div>
											<div class="form-group">
												<label for="parcoursComment" class="col-sm-3 control-label">Note</label>
												<div class="col-sm-9">
													<textarea name="parcoursComment" id="parcoursComment" cols="20" rows="6" tabIndex="9" class="form-control"></textarea>
												</div>
											</div>
											<p class="text-center"><input class="btn btn-primary" type="submit" value="Ajouter le parcours" tabindex="10"></p>							
										</form>	
									</div>
								</div>
							</div>
						</div>
					</div>	<!-- panel	-->
				</div>	<!-- col-md-4 -->
			</div>					
		</div>	<!-- container	-->

		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    </body>
</html>
