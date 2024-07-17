<?php
// Ensure session is started
require '../db_config.php';
include('../return_to_login_page.php');

// Check if the user is logged in
if (!isset($_SESSION['authentification_id']) || !isset($_SESSION['role']) || !isset($_SESSION['EmployeID'])) {
    // Redirect to login page if not logged in
    header('Location: ../login_process.php');
    exit(); // Ensure that script stops here to prevent further execution
}

$employe_id = $_SESSION['EmployeID'];

// Fetch main objectives assigned to the logged-in employee
$sql = "SELECT * FROM Objectifs WHERE EmployeID = :employe_id AND ParentObjectifID IS NULL AND Statut != 'Terminé'";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':employe_id', $employe_id, PDO::PARAM_INT);
$stmt->execute();
$main_objectives = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch sub-objectives for each main objective
foreach ($main_objectives as &$main_objective) {
    $sql = "SELECT * FROM Objectifs WHERE ParentObjectifID = :parent_objectif_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':parent_objectif_id', $main_objective['ObjectifID'], PDO::PARAM_INT);
    $stmt->execute();
    $main_objective['sub_objectives'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
unset($main_objective);

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['objectif_id']) && isset($_POST['status'])) {
    $objectif_id = $_POST['objectif_id'];
    $status = $_POST['status'];

    // Update the status of the objective
    $sql = "UPDATE Objectifs SET Statut = :status WHERE ObjectifID = :objectif_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':objectif_id', $objectif_id, PDO::PARAM_INT);
    $stmt->execute();

    // Check if this is a sub-objective being updated
    $is_sub_objective = false;
    foreach ($main_objectives as $main_objective) {
        foreach ($main_objective['sub_objectives'] as $sub_objective) {
            if ($sub_objective['ObjectifID'] == $objectif_id) {
                $is_sub_objective = true;
                break 2; // Exit both loops
            }
        }
    }

    // If it's a sub-objective, check the status of its main objective
    if ($is_sub_objective) {
        foreach ($main_objectives as &$main_objective) {
            if ($main_objective['ObjectifID'] == $objectif_id) {
                // Check if all sub-objectives are Terminé
                $all_sub_terminated = true;
                foreach ($main_objective['sub_objectives'] as $sub_obj) {
                    if ($sub_obj['Statut'] != 'Terminé') {
                        $all_sub_terminated = false;
                        break;
                    }
                }
                // Update main objective status if needed
                if ($all_sub_terminated) {
                    $main_objective['Statut'] = 'Terminé';
                    $sql = "UPDATE Objectifs SET Statut = 'Terminé' WHERE ObjectifID = :objectif_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':objectif_id', $main_objective['ObjectifID'], PDO::PARAM_INT);
                    $stmt->execute();
                }
                break; // Exit the loop since we found the main objective
            }
        }
        unset($main_objective);
    }

    // Redirect to avoid form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Calculate card counts
$sql = "SELECT SUM(TIMESTAMPDIFF(HOUR, Heure_Arrivee, Heure_Depart)) AS total_hours FROM présence_journalière WHERE EmployéID = :employe_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':employe_id', $employe_id, PDO::PARAM_INT);
$stmt->execute();
$total_hours = $stmt->fetchColumn();

$sql = "SELECT COUNT(DISTINCT Date) AS total_days FROM présence_journalière WHERE EmployéID = :employe_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':employe_id', $employe_id, PDO::PARAM_INT);
$stmt->execute();
$total_days = $stmt->fetchColumn();

$sql = "SELECT SUM(DATEDIFF(Date_Fin, Date_Début) + 1) AS total_leave_days FROM Congé WHERE EmployéID = :employe_id AND Statut = 'Approved'";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':employe_id', $employe_id, PDO::PARAM_INT);
$stmt->execute();
$total_leave_days = $stmt->fetchColumn();

// Fetch chart data
$sql = "SELECT YEARWEEK(Date, 1) AS week, SUM(TIMESTAMPDIFF(HOUR, Heure_Arrivee, Heure_Depart)) AS hours FROM présence_journalière WHERE EmployéID = :employe_id GROUP BY YEARWEEK(Date, 1) ORDER BY week";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':employe_id', $employe_id, PDO::PARAM_INT);
$stmt->execute();
$work_hours_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT Mois AS month, 
        COUNT(CASE WHEN Jour1 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour2 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour3 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour4 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour5 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour6 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour7 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour8 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour9 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour10 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour11 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour12 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour13 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour14 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour15 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour16 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour17 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour18 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour19 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour20 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour21 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour22 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour23 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour24 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour25 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour26 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour27 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour28 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour29 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour30 = 'P' THEN 1 END) + 
        COUNT(CASE WHEN Jour31 = 'P' THEN 1 END) AS days 
        FROM toutPrésence 
        WHERE EmployéID = :employe_id 
        GROUP BY Mois 
        ORDER BY Mois";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':employe_id', $employe_id, PDO::PARAM_INT);
$stmt->execute();
$work_days_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="../assets/plugins/morris/morris.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/stylee.css">
    <style>
        .morris-chart {
            height: 300px;
        }

        .tracker-widget {
            margin-top: 30px;
        }

        .tracker-widget .goal-card {
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .tracker-widget .goal-card h4 {
            margin-bottom: 10px;
        }

        .tracker-widget .goal-card p {
            color: #6c757d;
        }

        .tracker-widget .progress-bar {
            height: 10px;
            margin-top: 5px;
        }

        .board1 {
            height: 100%;
            padding: 30px;
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
                    <div class="row">
                        <div class="col-sm-12 mt-5">
                            <h3 class="page-title mt-3">Bienvenue Utilisateur !</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item active">Table De Bord</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card board1 fill">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <div>
                                        <h3 class="card_widget_header"><?php echo $total_hours; ?></h3>
                                        <h6 class="text-muted">Total Hours de travail</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card board1 fill">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <div>
                                        <h3 class="card_widget_header"><?php echo $total_days; ?></h3>
                                        <h6 class="text-muted">Total Jours de Travail</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card board1 fill">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <div>
                                        <h3 class="card_widget_header"><?php echo $total_leave_days; ?></h3>
                                        <h6 class="text-muted">Total Jours Conge</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Heures de travail par semaine</h5>
                                <div id="workHoursChart" class="morris-chart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Jours de travail par mois</h5>
                                <div id="workDaysChart" class="morris-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row tracker-widget">
                    <?php foreach ($main_objectives as $main_objective) : ?>
                        <div class="col-md-6">
                            <div class="card goal-card">
                                <h4><?php echo htmlspecialchars($main_objective['Titre']); ?></h4>
                                <?php
                                // Calculate progress based on sub-objectives
                                $total_sub_objectives = count($main_objective['sub_objectives']);
                                $completed_sub_objectives = 0;

                                foreach ($main_objective['sub_objectives'] as $sub_objective) {
                                    if ($sub_objective['Statut'] == 'Terminé') {
                                        $completed_sub_objectives++;
                                    }
                                }

                                if ($total_sub_objectives > 0) {
                                    $progress_percentage = ($completed_sub_objectives / $total_sub_objectives) * 100;
                                } else {
                                    $progress_percentage = 0;
                                }
                                ?>
                                <p>Progression: <?php echo round($progress_percentage, 2); ?>%</p>
                                <div class="progress">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $progress_percentage; ?>%;" aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
    <script src="../assets/js/script.js"></script>
    <script>
        $(document).ready(function() {
            var workHoursData = <?php echo json_encode($work_hours_data); ?>;
            var workDaysData = <?php echo json_encode($work_days_data); ?>;

            Morris.Line({
                element: 'workHoursChart',
                data: workHoursData,
                xkey: 'week',
                ykeys: ['hours'],
                labels: ['Hours'],
                resize: true,
                lineColors: ['#007bff']
            });

            Morris.Bar({
                element: 'workDaysChart',
                data: workDaysData,
                xkey: 'month',
                ykeys: ['days'],
                labels: ['Days'],
                resize: true,
                barColors: ['#28a745']
            });
        });
    </script>
</body>

</html>
