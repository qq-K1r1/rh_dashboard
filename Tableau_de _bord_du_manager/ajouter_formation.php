<?php
// Ensure session is started at the very beginning
require '../db_config.php';
include('../return_to_login_page.php');

$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$nomFormation = $_POST['newFormationName'];
	$description = $_POST['newFormationDescription'];
	$dateFormation = $_POST['newFormationDate'];
	$duree = $_POST['newFormationDuration'];

	// Prepare SQL statement
	$sql = "INSERT INTO Formation (Nom_Formation, Description, Date_Formation, Duree) VALUES (:nomFormation, :description, :dateFormation, :duree)";
	$stmt = $pdo->prepare($sql);

	// Bind parameters
	$stmt->bindValue(':nomFormation', $nomFormation, PDO::PARAM_STR);
	$stmt->bindValue(':description', $description, PDO::PARAM_STR);
	$stmt->bindValue(':dateFormation', $dateFormation, PDO::PARAM_STR);
	$stmt->bindValue(':duree', $duree, PDO::PARAM_STR);

	// Execute SQL statement and check if it was successful
	if ($stmt->execute()) {
		$successMessage = 'Formation ajoutée avec succès!';
	} else {
		$successMessage = 'Erreur lors de l\'ajout de la formation: ' . implode(', ', $stmt->errorInfo());
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<title>System De Gestion Resources Humaines</title>
	<link rel="shortcut icon" type="image/x-icon" href="../assets/img/hrLogo.png">
	<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="../assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="../assets/plugins/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="../assets/css/feathericon.min.css">
	<link rel="stylesheet" href="../assets/plugins/morris/morris.css">
	<link rel="stylesheet" href="../assets/css/style.css">
	<link rel="stylesheet" href="../assets/css/stylee.css">
</head>

<body>
	<div class="main-wrapper">
		<?php include('header.php'); ?>
		<?php include('sidebar.php'); ?>
		<div class="page-wrapper">
			<div class="content container-fluid">
				<div class="page-header">
					<div class="row">
						<div class="col-sm-12 mt-5">
							<h3 class="page-title mt-3">Liste des Formations</h3>
							<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="manager.php">Tableau De Bord</a></li>
								<li class="breadcrumb-item active">Formation</li>
							</ul>
						</div>
					</div>
				</div>

				<!-- Manager Section to Add New Formation -->
				<div class="row">
					<div class="col-md-12">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title">Ajouter une Nouvelle Formation</h4>
							</div>
							<div class="card-body">
								<?php if ($successMessage) : ?>
									<div class="alert alert-success">
										<?php echo $successMessage; ?>
									</div>
								<?php endif; ?>
								<form method="POST" action="">
									<div class="form-group">
										<label for="newFormationName">Nom de la Formation</label>
										<input type="text" class="form-control" id="newFormationName" name="newFormationName" required>
									</div>
									<div class="form-group">
										<label for="newFormationDescription">Description</label>
										<textarea class="form-control" id="newFormationDescription" name="newFormationDescription" rows="3" required></textarea>
									</div>
									<div class="form-group">
										<label for="newFormationDate">Date de la Formation</label>
										<input type="date" class="form-control" id="newFormationDate" name="newFormationDate" required>
									</div>
									<div class="form-group">
										<label for="newFormationDuration">Durée</label>
										<input type="text" class="form-control" id="newFormationDuration" name="newFormationDuration" required>
									</div>
									<button type="submit" class="btn btn-primary">Ajouter</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="../assets/js/jquery-3.5.1.min.js"></script>
	<script src="../assets/js/popper.min.js"></script>
	<script src="../assets/js/bootstrap.min.js"></script>
	<script src="../assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
	<script src="../assets/plugins/raphael/raphael.min.js"></script>
	<script src="../assets/plugins/morris/morris.min.js"></script>
	<script src="../assets/js/chart.morris.js"></script>
	<script src="../assets/js/script.js"></script>
</body>

</html>