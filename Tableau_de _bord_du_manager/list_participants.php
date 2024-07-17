<?php
require '../db_config.php';
include('../return_to_login_page.php');

// Function to fetch participants with formation details
function fetchParticipantsWithFormation($pdo)
{
    $sql = "SELECT f.Nom_Formation, e.NomPrenom, f.Date_Formation, f.Duree, f.Description
            FROM EmployeeFormation ef
            INNER JOIN Formation f ON ef.FormationID = f.FormationID
            INNER JOIN Employé e ON ef.EmployeID = e.EmployéID";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch participants data
$participants = fetchParticipantsWithFormation($pdo);

// Define the number of columns per page
$columns_per_page = 6;

// Get the number of columns dynamically
$columns = array_keys($participants[0]);

// Calculate the number of pages
$total_columns = count($columns);
$num_pages = ceil($total_columns / $columns_per_page);

// Fetch the current page number from the URL, default is 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Calculate the starting column index
$start_column = ($page - 1) * $columns_per_page;
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
        /* Additional CSS for table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }

        table th,
        table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
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
                    <div class="row">
                        <div class="col-sm-12 mt-5">
                            <h3 class="page-title mt-3">Liste des Participants</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="manager.php">Tableau De Bord</a></li>
                                <li class="breadcrumb-item active">Participants</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Participants Section -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Liste des Participants (Page <?php echo $page; ?>)</h4>
                            </div>
                            <div class="card-body">
                                <div id="participantsList">
                                    <table>
                                        <thead>
                                            <tr>
                                                <?php
                                                for ($i = $start_column; $i < min($start_column + $columns_per_page, $total_columns); $i++) {
                                                    echo '<th>' . htmlspecialchars($columns[$i]) . '</th>';
                                                }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($participants as $participant): ?>
                                                <tr>
                                                    <?php
                                                    for ($i = $start_column; $i < min($start_column + $columns_per_page, $total_columns); $i++) {
                                                        echo '<td>' . htmlspecialchars($participant[$columns[$i]]) . '</td>';
                                                    }
                                                    ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Participants Section -->

                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>">Précédent</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $num_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $num_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>">Suivant</a>
                    <?php endif; ?>
                </div>
                <!-- End of Pagination -->

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
                        