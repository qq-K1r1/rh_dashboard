<?php
require '../db_config.php';
include('../return_to_login_page.php');

// Check if the necessary session variables are set
if (!isset($_SESSION['authentification_id']) || !isset($_SESSION['role']) || !isset($_SESSION['EmployeID'])) {
    // Redirect to login page if not logged in
    header('Location: ../login_process.php');
    exit(); // Ensure that script stops here to prevent further execution
}

// Assume that the employee's ID is stored in the session
$employeeID = $_SESSION['EmployeID'];

// Fetch leave requests for the logged-in employee
try {
    $sql = "SELECT c.CongéID, e.NomPrenom, c.Type_Congé, c.Date_Début, c.Date_Fin, c.Motif, c.Statut
            FROM Congé c
            JOIN Employé e ON c.EmployéID = e.EmployéID
            WHERE e.EmployéID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employeeID]);
    $leave_requests = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
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
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="mt-5">
                                <h4 class="card-title float-left mt-2">Mes Demandes de Congé</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container mt-5">
                    <h2 class="mb-4">Historique des Demandes de Congé</h2>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Employé</th>
                                                    <th>Type de congé</th>
                                                    <th>Du</th>
                                                    <th>Au</th>
                                                    <th>Nombre de jours</th>
                                                    <th>Motif</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($leave_requests)) : ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center">Aucune demande de congé disponible.</td>
                                                    </tr>
                                                <?php else : ?>
                                                    <?php foreach ($leave_requests as $request) : ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($request['NomPrenom']); ?></td>
                                                            <td><?php echo htmlspecialchars($request['Type_Congé']); ?></td>
                                                            <td><?php echo htmlspecialchars($request['Date_Début']); ?></td>
                                                            <td><?php echo htmlspecialchars($request['Date_Fin']); ?></td>
                                                            <td>
                                                                <?php 
                                                                $start_date = new DateTime($request['Date_Début']);
                                                                $end_date = new DateTime($request['Date_Fin']);
                                                                $interval = $start_date->diff($end_date);
                                                                echo $interval->days + 1 . " Jours"; // Including the start date
                                                                ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($request['Motif']); ?></td>
                                                            <td><?php echo htmlspecialchars($request['Statut']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
            </div>
        </div>
    </div>
</body>

</html>
