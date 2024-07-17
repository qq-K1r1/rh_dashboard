<?php
// Ensure session is started at the very beginning
require '../db_config.php';
include('../return_to_login_page.php');

// Initialize variables for success message and errors
$success_message = "";
$error_message = "";

// Fetch employee name from session
$employeeName = $_SESSION['full_name'] ?? '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare data for insertion (sanitize and validate as needed)
    $employeeName = $_POST['employee']; // Retain for display purpose, not used for submission
    $leaveType = $_POST['leaveType'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $reason = $_POST['reason'];
    $status = "En attente"; // Assuming initial status is pending

    try {
        // Fetch the EmployéID based on the authenticated session
        $employeeID = $_SESSION['EmployeID'];

        // SQL to insert data into Congé table
        $sql = "INSERT INTO Congé (Type_Congé, Date_Début, Date_Fin, Motif, Statut, EmployéID)
                VALUES (?, ?, ?, ?, ?, ?)";

        // Prepare statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(1, $leaveType);
        $stmt->bindParam(2, $startDate);
        $stmt->bindParam(3, $endDate);
        $stmt->bindParam(4, $reason);
        $stmt->bindParam(5, $status);
        $stmt->bindParam(6, $employeeID);

        // Execute statement
        $stmt->execute();

        // Set success message
        $success_message = "Demande de congé soumise avec succès !";
    } catch (PDOException $e) {
        // Set error message
        $error_message = "Error: " . $e->getMessage();
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
    <style>
        .page-wrapper {
            margin-top: 100px;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">

        <?php include('header.php'); ?>
        <?php include('sidebar.php'); ?>

        <div class="page-wrapper">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-10">
                        <div class="card">
                            <h3 class="card-header text-left">
                                Demander Congé
                            </h3>
                            <div class="card-body">
                                <?php
                                // Display success or error message
                                if (!empty($success_message)) {
                                    echo '<div class="alert alert-success">' . $success_message . '</div>';
                                }
                                if (!empty($error_message)) {
                                    echo '<div class="alert alert-danger">' . $error_message . '</div>';
                                }
                                ?>
                                <form id="leaveRequestForm" method="post">
                                    <div class="form-group">
                                        <label for="employee">Employé</label>
                                        <input type="text" class="form-control" id="employee" name="employee" value="<?php echo htmlspecialchars($employeeName); ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="leaveType">Type de congé</label>
                                        <select class="form-control" id="leaveType" name="leaveType">
                                            <option>Congé annuel</option>
                                            <option>Congé maladie</option>
                                            <option>Congé maternité/paternité</option>
                                            <option>Congé sans solde</option>
                                            <option>Autre</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="startDate">Du</label>
                                        <input type="date" class="form-control" id="startDate" name="startDate">
                                    </div>
                                    <div class="form-group">
                                        <label for="endDate">Au</label>
                                        <input type="date" class="form-control" id="endDate" name="endDate">
                                    </div>
                                    <div class="form-group">
                                        <label for="reason">Motif</label>
                                        <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Motif du congé"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">Soumettre</button>
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
