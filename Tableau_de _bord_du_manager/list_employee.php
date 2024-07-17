<?php
// Ensure session is started at the very beginning
require '../db_config.php';
include('../return_to_login_page.php');

// Initialize variables for search
$search_term = "";
$where_clause = "";

// Define the number of results per page
$results_per_page = 10;

// Fetch the current page number from the URL, default is 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Calculate the starting row of the results
$start_from = ($page - 1) * $results_per_page;

// Check if a search term is submitted
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    // Prepare WHERE clause for the query
    $where_clause = "WHERE e.NomPrenom LIKE :search_term OR e.Nom_utilisateur LIKE :search_term";
}

// Check if a delete request is submitted
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Query to delete the employee by ID
    $delete_sql = "DELETE FROM Employé WHERE EmployéID = :delete_id";
    $delete_stmt = $pdo->prepare($delete_sql);
    $delete_stmt->bindParam(':delete_id', $delete_id, PDO::PARAM_INT);
    $delete_stmt->execute();

    // Redirect to avoid re-submission on page refresh
    header("Location: list_employee.php");
    exit;
}

// Query to fetch employees with department and authentication details
$sql = "SELECT e.EmployéID, e.NomPrenom, e.Nom_utilisateur, e.Email, e.Téléphone, e.Date_Embauche, d.Nom_Département, a.Role
        FROM Employé e
        LEFT JOIN Département d ON e.DépartementID = d.DépartementID
        LEFT JOIN Authentification a ON e.AuthentificationID = a.AuthentificationID
        $where_clause
        LIMIT :start_from, :results_per_page";
$stmt = $pdo->prepare($sql);
if ($where_clause) {
    $stmt->bindValue(':search_term', '%' . $search_term . '%', PDO::PARAM_STR);
}
$stmt->bindValue(':start_from', $start_from, PDO::PARAM_INT);
$stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of employees for pagination
$total_sql = "SELECT COUNT(*) FROM Employé e LEFT JOIN Département d ON e.DépartementID = d.DépartementID LEFT JOIN Authentification a ON e.AuthentificationID = a.AuthentificationID $where_clause";
$total_stmt = $pdo->prepare($total_sql);
if ($where_clause) {
    $total_stmt->bindValue(':search_term', '%' . $search_term . '%', PDO::PARAM_STR);
}
$total_stmt->execute();
$total_employees = $total_stmt->fetchColumn();

// Calculate the total number of pages
$total_pages = ceil($total_employees / $results_per_page);
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
                                <h4 class="card-title float-left mt-2">Employee</h4>
                                <a href="ajouter_employee.php" class="btn btn-primary float-right veiwbutton">Ajouter
                                    Employee</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form method="GET" action="list_employee.php">
                            <div class="row formtype">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>ID Employé / Nom</label>
                                        <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="Entrez ID ou Nom">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Rechercher</label>
                                        <button type="submit" class="btn btn-success btn-block mt-0 search_button"> Rechercher </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="datatable table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Nom d'utilisateur</th>
                                                <th>Email</th>
                                                <th>Téléphone</th>
                                                <th>Date Embauche</th>
                                                <th>Département</th>
                                                <th>Rôle</th>
                                                <th class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($result as $row) {
                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row['NomPrenom']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['Nom_utilisateur']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['Téléphone']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['Date_Embauche']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['Nom_Département']) . "</td>";
                                                echo "<td><span class='custom-badge status-green'>" . htmlspecialchars($row['Role']) . "</span></td>";
                                                echo "<td class='text-right'>";
                                                echo "<div class='dropdown dropdown-action'>";
                                                echo "<a href='#' class='action-icon dropdown-toggle' data-toggle='dropdown' aria-expanded='false'><i class='fas fa-ellipsis-v ellipse_color'></i></a>";
                                                echo "<div class='dropdown-menu dropdown-menu-right'>";
                                                echo "<a class='dropdown-item' href='modifier_employee.php?id=" . htmlspecialchars($row['EmployéID']) . "'><i class='fas fa-pencil-alt m-r-5'></i> Modifier</a>";
                                                echo "<a class='dropdown-item' href='#' data-toggle='modal' data-target='#delete_employee' data-id='" . htmlspecialchars($row['EmployéID']) . "'><i class='fas fa-trash-alt m-r-5'></i> Supprimer</a>";
                                                echo "</div>";
                                                echo "</div>";
                                                echo "</td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div id="delete_employee" class="modal fade delete-modal" role="dialog">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body text-center">
                                <img src="../assets/img/sent.png" alt="" width="50" height="46">
                                <h3 class="delete_class">Êtes-vous sûr de vouloir supprimer cet employé?</h3>
                                <div class="m-t-20">
                                    <a href="#" class="btn btn-white" data-dismiss="modal">Annuler</a>
                                    <a href="#" id="confirm_delete" class="btn btn-danger">Supprimer</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Delete Confirmation Modal -->
                
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
    <script>
        $(document).ready(function(){
            $('#delete_employee').on('show.bs.modal', function(e) {
                var employeeId = $(e.relatedTarget).data('id');
                $('#confirm_delete').attr('href', 'list_employee.php?delete_id=' + employeeId);
            });
        });
    </script>
</body>

</html>
