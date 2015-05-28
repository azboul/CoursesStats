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

/*
 *
 * Liste des requêtes SQLite via php
 *
**/

$queryGeneralInfos = $theDatabase->query('select DateCreation, DistanceTot,time(TempsTot, "unixepoch") as TimeTot from General');
while ($row = $queryGeneralInfos->fetchArray()) {
	$createDate = $row['DateCreation'];
	$sumDist = $row['DistanceTot'];
	$sumTime = $row['TimeTot'];
	break;
}
$sumDist = $sumDist.'km';
list( $h, $m, $s) = explode(":", $sumTime);
$sumTime = $h.'h '.$m.'min '.$s.'s';

$queryAllSorties = $theDatabase->query('select S.Date, S.Parcours, time(S.Temps, "unixepoch") as TempsFormat, S.Vitesse, S.Commentaire, P.Nom, P.Distance from Sorties S, Parcours P where S.Parcours = P.Nom order by date(S.Date) DESC');

$queryAllParcours = $theDatabase->query('Select Nom, Lieux, Distance, Note from Parcours');
?>
<html>
    <head>
        <title>Statistiques de course</title>
        <link rel="icon" href="favicon.ico?v=2" />
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

        <link rel="stylesheet" type="text/css" href="css/style.css" />

        <script type="text/javascript">
            function unhide(divID)
            {
                var item = document.getElementById(divID);
                if (item) {
                    item.className=(item.className=='hidden')?'unhidden':'hidden';
                }
            }
        </script>

	</head>

    <body>
<!-- Entête
================================================== -->
		<nav class="navbar navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#"><span class="glyphicon glyphicon-home"></span> Statistiques de course</a>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<form class="navbar-form navbar-right">
						<output><span class="glyphicon glyphicon-time"></span> <?php echo $createDate ?></output>
					</form>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#sorties">Sorties</a></li>
						<li><a href="#parcours">Parcours</a></li>
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</nav>

<!-- Résumé
================================================== -->
		<header class="mainHeader">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
                        <h1>Résumé</h1>
                        <h3><span class="glyphicon glyphicon-chevron-right"></span> Distance totale : <small><?php echo $sumDist ?> </small></h3>
                        <h3><span class="glyphicon glyphicon-chevron-right"></span>Temps total : <small><?php echo $sumTime ?> </small></h3>
					</div>
				</div>
			</div>
		</header>

        <section >
	    	<div class="container-fluid  sortiesSection">
                <hr>
            </div>
        </section>

<!-- Formulaire : Sortie
================================================== -->
        <section id="sorties">
	    	<div class="container-fluid  sortiesSection">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h2>Sorties</h2>
                    </div>
                </div>
				<div class="row ">
                    <div class="col-lg-6 col-lg-offset-3">
                        <div class="text-center formS">
                            <button class="btn-sorties btn-lg" onclick="javascript:unhide('formSortie');" ><span class="glyphicon glyphicon-plus-sign"></span> Nouvelle sortie</button>
                        </div>
                        <div id="formSortie"class="hidden">
                            <div class="panel panel-default formSortie">
                                <div class="panel-body">
									<form action="record_Sortie.php" method="post" class="form-horizontal">
										<fieldset>
											<div class="form-group">
												<label for="sortieDate" class="col-sm-3 control-label">Date</label>
												<div class="col-sm-9"><input name="sortieDate" id="sortieDate" type="date" tabIndex="1" placeholder="YYYY-MM-JJ"></div>
											</div>
											<div class="form-group">
												<label for="sortieId" class="col-sm-3 control-label">Parcours</label>
												<div class="col-sm-9">
													<select name="sortieId" id="sortieId" tabIndex="2" class="form-control">
<?php while ($row = $queryAllParcours->fetchArray()) { ?>
														<option >
                                                            <?php echo( htmlspecialchars($row['Nom']));?>
                                                        </option>
<?php	} ?>
    												</select>
    											</div>
    										</div>
    										<div class="form-group">
    											<label for="sortieTime" class="col-sm-3 control-label">Temps</label>
    											<div class="col-sm-9"><input name="sortieTime" id="sortieTime" type="time" tabIndex="3" placeholder="HH:MM:SS"></div>
    										</div>
    										<div class="form-group">
    											<label for="sortieComment" class="col-sm-3 control-label">Note</label>
    											<div class="col-sm-9"><textarea name="sortieComment" id="sortieComment" cols="10" rows="4" tabIndex="4" class="form-control"></textarea></div>
    										</div>
    										<p class="text-center"><input class="btn-sorties btn-lg btn-block" type="submit" value="Ajouter la sortie" tabindex="5"></p>
    									</fieldset>
    								</form>
                                </div>
                            </div>
    					</div>
					</div>	<!-- col-lg-12 -->
				</div>	<!-- row -->

<!-- Sorties
================================================== -->
				<div class="row">
					<div class="col-lg-8 col-lg-offset-2">
						<div>
								<div class="row">
									<div class="table-responsive">
										<table class="table">
											<thead>
												<tr >
													<th scope="col">Date</th>
													<th scope="col">Parcours</th>
													<th scope="col">Temps</th>
													<th scope="col">Vitesse</th>
												</tr>
											</thead>
											<tbody>
<?php
while ($row = $queryAllSorties->fetchArray()) {
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
													<td><p><?php echo(htmlspecialchars($date));?>
<?php if( $comment)	{ ?>
													<small><i>
														<abbr title=<?php echo('"'.htmlspecialchars($comment).'"'); ?> ><span class="glyphicon glyphicon-info-sign"></span></abbr>
													</i></small>
<?php } ?>
                                                    </p>
													</td>
													<td>
                                                        <p>
                                                            <?php echo(htmlspecialchars($parcours));?>
                                                            <small ><?php echo(htmlspecialchars($distance));?> km </small>
                                                        </p>
													</td>
													<td><?php echo(htmlspecialchars($temps));?></td>
													<td><?php echo(htmlspecialchars($vitesse));?> km/h</td>
												</tr>
<?php } ?>
											</tbody>
										</table>
									</div>
								</div> <!-- div -->
    						</div>
    					</div>  <!-- col-lg-12 -->
    				</div>	<!-- row -->
    			</div>	<!-- containter-->
        </section>

        <section>
	    	<div class="container-fluid  sortiesSection">
                <hr>
            </div>
        </section>

<!-- Formulaire : Parcours
================================================== -->
        <section id="parcours">
	    	<div class="container-fluid parcoursSection">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <h2>Parcours</h2>
                    </div>
                </div>
				<div class="row">
                    <div class="col-lg-6 col-lg-offset-3">
                        <div class="text-center formS">
                            <button class="btn-parcours btn-lg" onclick="javascript:unhide('formParcours');" ><span class="glyphicon glyphicon-plus-sign"></span> Nouveau parcours</button>
                        </div>
                            <div id="formParcours" class="hidden">
                                <div class="panel panel-default formParcours">
                                    <div class="panel-body">
    									<form action="record_Parcours.php" method="post" class="form-horizontal ">
    										<fieldset>
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
    													<textarea name="parcoursComment" id="parcoursComment" cols="10" rows="4" tabIndex="9" class="form-control"></textarea>
    												</div>
    											</div>
    											<p class="text-center"><input class="btn-parcours btn-lg btn-block" type="submit" value="Ajouter le parcours" tabindex="10"></p>
    										</fieldset>
    									</form>
                                    </div>
								</div>
                            </div>
						</div> <!--class="col-lg-12">-->
				</div>	<!-- row -->

<!-- Parcours
================================================== -->
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
						<div class="row">
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th scope="col">Nom</th>
											<th scope="col">Distance</th>
											<th scope="col">Note</th>
										</tr>
									</thead>
									<tbody>
<?php
while ($row = $queryAllParcours->fetchArray()) {
	//var_dump($row);
	//$id = $row['Id'];
	$nom = $row['Nom'];
	$lieux = $row['Lieux'];
	$comment = $row['Note'];
	$distance = $row['Distance'];
?>
										<tr>
											<td><h4><?php echo(htmlspecialchars($nom));?> <small><?php echo(htmlspecialchars($lieux));?> <small></h4></td>
											<td><i><?php echo(htmlspecialchars($distance));?> km</i></td>
											<td><?php echo(htmlspecialchars($comment));?></td>
										</tr>
<?php } ?>
									</tbody>
								</table>
							</div>
						</div> <!-- div row -->
    				</div>	<!--col-md-8-->
    			</div>
    		</div>	<!-- row -->
        </div>  <!--container-->
        </section>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
	</body>
</html>

<?php
/*
 *
 * Fermeture des requêtes SQLite via php
 *
**/

$queryGeneralInfos->finalize();
$queryAllSorties->finalize();
$queryAllParcours->finalize();
?>
