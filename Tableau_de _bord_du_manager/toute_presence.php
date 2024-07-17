<?php
// Ensure session is started at the very beginning
require '../db_config.php';
include('../return_to_login_page.php');

// Get the current month and year
$currentMonth = date('n');
$currentYear = date('Y');

// Check for selected month and year from the form
$selectedMonth = $_GET['month'] ?? $currentMonth;
$selectedYear = $_GET['year'] ?? $currentYear;
$searchTerm = $_GET['search'] ?? '';

// Fetch presence data for all employees for the selected month and year
$presenceQuery = $pdo->prepare("
    SELECT e.EmployéID, e.nomPrenom, p.Date, p.Heure_Arrivee, p.Heure_Depart 
    FROM Employé e
    LEFT JOIN présence_journalière p ON e.EmployéID = p.EmployéID AND MONTH(p.Date) = ? AND YEAR(p.Date) = ?
    WHERE e.nomPrenom LIKE ?
    ORDER BY e.nomPrenom, p.Date
");
$presenceQuery->execute([$selectedMonth, $selectedYear, '%' . $searchTerm . '%']);

$presenceData = $presenceQuery->fetchAll(PDO::FETCH_ASSOC);

// Determine the number of days in the selected month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);

// Initialize an array to hold the presence status for each employee
$employeesPresence = [];

foreach ($presenceData as $data) {
	$employeeID = $data['EmployéID'];
	$employeeName = $data['nomPrenom'];
	$date = $data['Date'];
	$arrivalTime = $data['Heure_Arrivee'];
	$departureTime = $data['Heure_Depart'];

	if (!isset($employeesPresence[$employeeID])) {
		$employeesPresence[$employeeID] = [
			'name' => $employeeName,
			'presence' => array_fill(1, $daysInMonth, null) // Default is null
		];
	}

	if ($date) {
		$day = (int)date('j', strtotime($date));
		if ($arrivalTime && $departureTime) {
			$hoursWorked = (strtotime($departureTime) - strtotime($arrivalTime)) / 3600;

			if ($hoursWorked >= 8) {
				$employeesPresence[$employeeID]['presence'][$day] = 'P'; // Present
			} elseif ($hoursWorked >= 4) {
				$employeesPresence[$employeeID]['presence'][$day] = 'D'; // Half-day
			} else {
				$employeesPresence[$employeeID]['presence'][$day] = 'A'; // Default Absent for less than 4 hours
			}
		} else {
			$employeesPresence[$employeeID]['presence'][$day] = 'A'; // Default Absent if no arrival or departure time
		}
	}
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<title>System De Gestion Resources Humaines</title>
	<link rel="shortcut icon" type="image/x-icon" href="../assets/img/hrLogo.png">
	<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="../assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="../assets/plugins/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="../assets/css/feathericon.min.css">
	<link rel="stylesheet" href="https://cdn.oesmith.co.uk/morris-0.5.1.css">
	<link rel="stylesheet" href="../assets/plugins/morris/morris.css">
	<link rel="stylesheet" href="../assets/css/style.css">
	<link rel="stylesheet" href="../assets/css/stylee.css">
	<style>
		.page-wrapper {
			margin-top: 100px;
		}

		.table-responsive {
			box-shadow: 0 0.5em 1em -0.125em rgba(10, 10, 10, .1), 0 0 0 1px rgba(10, 10, 10, .02);
		}

		.fa-check {
			color: green;
		}

		.fa-times {
			color: red;
		}

		.fa-exclamation {
			color: orange;
		}
	</style>
</head>

<body>
	<div class="main-wrapper">
		<?php include('header.php'); ?>
		<?php include('sidebar.php'); ?>

		<div class="page-wrapper">
			<div class="content container-fluid">
				<div class="page-header">
					<div class="row align-items-center">
						<div class="col">
							<div class="mt-5">
								<h4 class="card-title float-left mt-2">Feuille de Présence</h4>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<form method="GET" action="">
							<div class="row formtype">
								<div class="col-md-3">
									<div class="form-group">
										<label>Sélectionner le Mois</label>
										<select class="form-control" id="sel1" name="month">
											<?php for ($m = 1; $m <= 12; $m++) : ?>
												<option value="<?= $m ?>" <?= $m == $selectedMonth ? 'selected' : '' ?>>
													<?= date('F', mktime(0, 0, 0, $m, 1)) ?>
												</option>
											<?php endfor; ?>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>Sélectionner l'Année</label>
										<select class="form-control" id="sel2" name="year">
											<?php for ($y = 2024; $y <= 2030; $y++) : ?>
												<option value="<?= $y ?>" <?= $y == $selectedYear ? 'selected' : '' ?>><?= $y ?></option>
											<?php endfor; ?>
										</select>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>Rechercher un Employé</label>
										<input type="text" class="form-control" name="search" placeholder="Nom de l'employé" value="<?= htmlspecialchars($searchTerm) ?>">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>Rechercher</label>
										<button type="submit" class="btn btn-success btn-block mt-0">Search</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="table-responsive">
							<table class="table table-striped custom-table mb-0">
								<thead>
									<tr>
										<th>Employee</th>
										<?php for ($i = 1; $i <= $daysInMonth; $i++) : ?>
											<th><?= $i ?></th>
										<?php endfor; ?>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($employeesPresence as $employeeID => $employeeData) : ?>
										<tr>
											<td><?= htmlspecialchars($employeeData['name']) ?></td>
											<?php foreach ($employeeData['presence'] as $status) : ?>
												<td>
													<?php if ($status === 'P') : ?>
														<i class="fas fa-check"></i>
													<?php elseif ($status === 'D') : ?>
														<i class="fas fa-exclamation"></i>
													<?php elseif ($status === 'A') : ?>
														<i class="fas fa-times"></i>
													<?php else : ?>
														-
													<?php endif; ?>
												</td>
											<?php endforeach; ?>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
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
	<script src="../assets/js/script.js"></script>
	<script src="../assets/js/select2.min.js"></script>
</body>

</html>