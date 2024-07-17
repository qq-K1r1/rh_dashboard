<?php
// Ensure session is started at the very beginning
require '../db_config.php';
include('../return_to_login_page.php');

// Check if a leave request is being approved or rejected
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && isset($_POST['leave_id'])) {
        $action = $_POST['action'];
        $leave_id = $_POST['leave_id'];
        $new_status = ($action == 'Approuvé') ? 'Approuvé' : 'Rejeté';

        try {
            // Update the status of the leave request
            $sql = "UPDATE Congé SET Statut = ? WHERE CongéID = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$new_status, $leave_id]);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

// Fetch leave requests from the database
try {
    $sql = "SELECT c.CongéID, e.NomPrenom, c.Type_Congé, c.Date_Début, c.Date_Fin, c.Motif, c.Statut
            FROM Congé c
            JOIN Employé e ON c.EmployéID = e.EmployéID
            WHERE c.Statut = 'En attente'";
    $stmt = $pdo->query($sql);
    $leave_requests = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
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
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="mt-5">
                                <h4 class="card-title float-left mt-2">Demande de congé</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container mt-5">
                    <h2 class="mb-4">Les Demandes de Congé</h2>
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
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="approvedRejectedRequests">
                                                <?php if (empty($leave_requests)) : ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center">Aucune demande de congé disponible.</td>
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
                                                                echo $interval->days . " Jours";
                                                                ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($request['Motif']); ?></td>
                                                            <td><?php echo htmlspecialchars($request['Statut']); ?></td>
                                                            <td>
                                                                <form method="post" style="display: inline;">
                                                                    <input type="hidden" name="leave_id" value="<?php echo $request['CongéID']; ?>">
                                                                    <input type="hidden" name="action" value="Approuvé">
                                                                    <button type="submit" class="btn btn-success btn-custom-sm">Approuver</button>
                                                                </form>
                                                                <form method="post" style="display: inline;">
                                                                    <input type="hidden" name="leave_id" value="<?php echo $request['CongéID']; ?>">
                                                                    <input type="hidden" name="action" value="Rejeté">
                                                                    <button type="submit" class="btn btn-danger btn-custom-sm">Rejeter</button>
                                                                </form>
                                                            </td>
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

                <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
            </div>
        </div>
    </div>
</body>

</html>