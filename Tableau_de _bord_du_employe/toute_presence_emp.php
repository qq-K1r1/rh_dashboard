<?php
// Ensure session is started at the very beginning
require '../db_config.php';
include('../return_to_login_page.php');

// Fetch employee ID and name from the session
$employeeID = $_SESSION['EmployeID'] ?? '';
$employeeName = $_SESSION['full_name'] ?? '';

if (!$employeeID) {
    // Redirect to login page if no employee ID in session
    header('Location: ../login.php');
    exit();
}

// Get the current month and year
$currentMonth = date('n');
$currentYear = date('Y');

// Check for selected month and year from the form
$selectedMonth = $_GET['month'] ?? $currentMonth;
$selectedYear = $_GET['year'] ?? $currentYear;

// Check if the employee has entered arrival and departure times
$presenceQuery = $pdo->prepare("SELECT Date, Heure_Arrivee, Heure_Depart FROM présence_journalière WHERE EmployéID = ? AND MONTH(Date) = ? AND YEAR(Date) = ?");
$presenceQuery->execute([$employeeID, $selectedMonth, $selectedYear]);

$presenceData = $presenceQuery->fetchAll(PDO::FETCH_ASSOC);

// Determine the number of days in the selected month
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);

// Initialize array to hold the presence status for each day
$presenceStatus = array_fill(1, $daysInMonth, null); // Default is null

foreach ($presenceData as $data) {
    $arrivalTime = $data['Heure_Arrivee'];
    $departureTime = $data['Heure_Depart'];
    $day = (int)date('j', strtotime($data['Date']));

    if ($arrivalTime && $departureTime) {
        $hoursWorked = (strtotime($departureTime) - strtotime($arrivalTime)) / 3600;

        if ($hoursWorked >= 8) {
            $presenceStatus[$day] = 'P'; // Present
        } elseif ($hoursWorked >= 4) {
            $presenceStatus[$day] = 'D'; // Half-day
        } else {
            $presenceStatus[$day] = 'A'; // Default Absent for less than 4 hours
        }
    } else {
        $presenceStatus[$day] = 'A'; // Default Absent if no arrival or departure time
    }
}

// Insert or update the presence data in the toutPrésence table
// Check if data for the current month and year exists
$checkQuery = $pdo->prepare("SELECT * FROM toutPrésence WHERE Mois = ? AND Année = ? AND EmployéID = ?");
$checkQuery->execute([$selectedMonth, $selectedYear, $employeeID]);
$existingData = $checkQuery->fetch(PDO::FETCH_ASSOC);

if ($existingData) {
    // Update the existing row
    $updateQuery = "UPDATE toutPrésence SET ";
    $updateColumns = [];
    foreach ($presenceStatus as $day => $status) {
        $updateColumns[] = "Jour$day = ?";
    }
    $updateQuery .= implode(', ', $updateColumns);
    $updateQuery .= " WHERE Mois = ? AND Année = ? AND EmployéID = ?";
    
    $stmt = $pdo->prepare($updateQuery);
    $params = array_merge(array_values($presenceStatus), [$selectedMonth, $selectedYear, $employeeID]);
    $stmt->execute($params);
} else {
    // Insert a new row
    $insertQuery = "INSERT INTO toutPrésence (Mois, Année, EmployéID, " . implode(',', array_map(function ($day) {
        return 'Jour' . $day;
    }, range(1, $daysInMonth))) . ") VALUES (?, ?, ?, " . implode(',', array_fill(0, $daysInMonth, '?')) . ")";
    
    $stmt = $pdo->prepare($insertQuery);
    $params = array_merge([$selectedMonth, $selectedYear, $employeeID], array_values($presenceStatus));
    $stmt->execute($params);
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
    <link rel="stylesheet" href="https://cdn.oesmith.co.uk/morris-0.5.1.css">
    <link rel="stylesheet" href="../assets/plugins/morris/morris.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/stylee.css">
    <style>
        .submenu_class {
            display: none;
            list-style-type: none;
            padding-left: 20px;
        }

        input[type="checkbox"] {
            display: none;
        }

        input[type="checkbox"]:checked + .submenu_class {
            display: block;
        }

        .submenu-toggle {
            cursor: pointer;
        }

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
                                    <tr>
                                        <td><?= htmlspecialchars($employeeName) ?></td>
                                        <?php foreach ($presenceStatus as $status) : ?>
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
    <script>
        $(document).ready(function() {
            $('#sidebar-menu ul li.submenu > label').click(function(e) {
                e.preventDefault();
                $(this).siblings('.submenu_class').slideToggle(200);
                $(this).parent().siblings().find('.submenu_class').slideUp(200);
            });
        });
    </script>
</body>
</html>
