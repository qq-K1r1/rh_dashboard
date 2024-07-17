<?php
require '../db_config.php';
include('../return_to_login_page.php');

// Define the number of results per page
$resultsPerPage = 12;

// Fetch search parameter
$searchName = isset($_GET['search_name']) ? $_GET['search_name'] : '';

// Calculate the total number of results in the database
try {
    $sqlCount = "SELECT COUNT(*) FROM présence_journalière
                 JOIN Employé ON présence_journalière.EmployéID = Employé.EmployéID
                 WHERE Employé.NomPrenom LIKE :search_name";
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute([':search_name' => '%' . $searchName . '%']);
    $totalResults = $stmtCount->fetchColumn();

    // Calculate the total number of pages needed
    $totalPages = ceil($totalResults / $resultsPerPage);

    // Get the current page from the URL, default to page 1 if not set
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Calculate the starting row for the SQL query
    $startRow = ($currentPage - 1) * $resultsPerPage;

    // SQL query to fetch attendance data with limit and offset
    $sql = "SELECT présence_journalière.EmployéID, Employé.NomPrenom AS full_name, présence_journalière.Date, présence_journalière.Heure_Arrivee, présence_journalière.Heure_Depart,
            CASE
                WHEN présence_journalière.Heure_Arrivee IS NOT NULL AND présence_journalière.Heure_Depart IS NOT NULL THEN TIMESTAMPDIFF(HOUR, présence_journalière.Heure_Arrivee, présence_journalière.Heure_Depart)
                ELSE NULL
            END AS total_hours
            FROM présence_journalière
            JOIN Employé ON présence_journalière.EmployéID = Employé.EmployéID
            WHERE Employé.NomPrenom LIKE :search_name
            ORDER BY présence_journalière.Date DESC
            LIMIT :start_row, :results_per_page";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search_name', '%' . $searchName . '%', PDO::PARAM_STR);
    $stmt->bindValue(':start_row', $startRow, PDO::PARAM_INT);
    $stmt->bindValue(':results_per_page', $resultsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $attendanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage(); // Output the actual PDO error message
    // Log or handle the error appropriately
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
                                <h4 class="card-title float-left mt-2">Présence Quotidienne</h4>
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
                                        <label>Rechercher par Nom</label>
                                        <input type="text" name="search_name" class="form-control" value="<?= htmlspecialchars($searchName) ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-success btn-block">Rechercher</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Employé</th>
                                                <th>Date</th>
                                                <th>Heure d'arrivée</th>
                                                <th>Heure de départ</th>
                                                <th>Heures Totales</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dailyAttendanceList">
                                            <?php foreach ($attendanceData as $row) : ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                                                    <td><?= htmlspecialchars($row['Date']) ?></td>
                                                    <td><?= htmlspecialchars($row['Heure_Arrivee']) ?></td>
                                                    <td><?= isset($row['Heure_Depart']) ? htmlspecialchars($row['Heure_Depart']) : '-' ?></td>
                                                    <td><?= isset($row['total_hours']) ? htmlspecialchars($row['total_hours']) : '-' ?></td>
                                                    <td>
                                                        <?php
                                                        if (isset($row['total_hours'])) {
                                                            echo $row['total_hours'] >= 8 ? 'Présent' : ($row['total_hours'] >= 4 ? 'Demi-journée' : 'Absent');
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php if ($totalPages > 1) : ?>
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-center">
                                            <?php if ($currentPage > 1) : ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $currentPage - 1 ?>&search_name=<?= htmlspecialchars($searchName) ?>" aria-label="Previous">
                                                        <span aria-hidden="true">&laquo;</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $i ?>&search_name=<?= htmlspecialchars($searchName) ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>
                                            <?php if ($currentPage < $totalPages) : ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $currentPage + 1 ?>&search_name=<?= htmlspecialchars($searchName) ?>" aria-label="Next">
                                                        <span aria-hidden="true">&raquo;</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                <?php endif; ?>
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
