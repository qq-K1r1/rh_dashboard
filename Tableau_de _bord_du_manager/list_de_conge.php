<?php
// Ensure session is started at the very beginning
require '../db_config.php';
include('../return_to_login_page.php');

// Define the number of results per page
$results_per_page = 10;

// Fetch the current page number from the URL, default is 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Calculate the starting row of the results
$start_from = ($page - 1) * $results_per_page;

try {
    // Get the total number of leave requests for pagination
    $total_sql = "SELECT COUNT(*) FROM Congé";
    $total_result = $pdo->query($total_sql);
    $total_requests = $total_result->fetchColumn();

    // Fetch leave requests for the current page
    $sql = "SELECT c.CongéID, e.NomPrenom, c.Type_Congé, c.Date_Début, c.Date_Fin, c.Motif, c.Statut
            FROM Congé c
            JOIN Employé e ON c.EmployéID = e.EmployéID
            LIMIT $start_from, $results_per_page";
    $stmt = $pdo->query($sql);
    $leave_requests = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Calculate the total number of pages
$total_pages = ceil($total_requests / $results_per_page);
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
        .filter-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .filter-section input, .filter-section select {
            width: 48%;
        }
        .pagination {
            display: flex;
            justify-content: center;
        }
        .pagination a {
            margin: 0 5px;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            color: #007bff;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
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
                                <h4 class="card-title float-left mt-2">Demande de congé</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container mt-5">
                    <h2 class="mb-4">Historique des Demandes de Congé</h2>
                    <div class="filter-section">
                        <input type="text" id="searchEmployee" class="form-control" placeholder="Rechercher par nom d'employé">
                        <select id="filterStatus" class="form-control">
                            <option value="all">Tous les statuts</option>
                            <option value="En attente">En attente</option>
                            <option value="Approuvé">Approuvé</option>
                            <option value="Rejeté">Rejeté</option>
                        </select>
                    </div>
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
                                            <tbody id="leaveRequests">
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
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php if (empty($leave_requests)) : ?>
                                        <p class="text-center">Aucune demande de congé disponible.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>">Précédent</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>">Suivant</a>
                    <?php endif; ?>
                </div>

                <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
                <script>
                    $(document).ready(function() {
                        var leaveRequests = <?php echo json_encode($leave_requests); ?>;

                        function filterTable() {
                            var employeeSearch = $('#searchEmployee').val().toLowerCase();
                            var statusFilter = $('#filterStatus').val();

                            $('#leaveRequests').empty();

                            leaveRequests.forEach(function(request) {
                                var employeeName = request.NomPrenom.toLowerCase();
                                var requestStatus = request.Statut;

                                var matchEmployee = employeeName.includes(employeeSearch);
                                var matchStatus = (statusFilter === 'all') || (requestStatus === statusFilter);

                                if (matchEmployee && matchStatus) {
                                    $('#leaveRequests').append(
                                        `<tr>
                                            <td>${request.NomPrenom}</td>
                                            <td>${request.Type_Congé}</td>
                                            <td>${request.Date_Début}</td>
                                            <td>${request.Date_Fin}</td>
                                            <td>${(new Date(request.Date_Fin) - new Date(request.Date_Début)) / (1000 * 60 * 60 * 24) + 1} Jours</td>
                                            <td>${request.Motif}</td>
                                            <td>${request.Statut}</td>
                                        </tr>`
                                    );
                                }
                            });

                            if ($('#leaveRequests').children().length === 0) {
                                $('#leaveRequests').append('<tr><td colspan="7" class="text-center">Aucune demande de congé disponible.</td></tr>');
                            }
                        }

                        $('#searchEmployee').on('keyup', filterTable);
                        $('#filterStatus').on('change', filterTable);

                        filterTable();
                    });
                </script>
            </div>
        </div>
    </div>
</body>

</html>
