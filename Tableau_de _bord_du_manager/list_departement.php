<?php
// Ensure session is started at the very beginning
require '../db_config.php';
include('../return_to_login_page.php');

// Check if delete_id parameter is set in GET request
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    try {
        // Delete department
        $sqlDelete = "DELETE FROM Département WHERE DépartementID = :id";
        $stmt = $pdo->prepare($sqlDelete);
        $stmt->bindValue(':id', $deleteId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            echo "Department deleted successfully.";
            exit; // Stop further execution after deletion
        } else {
            echo "Error deleting department.";
            exit; // Stop further execution on error
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit; // Stop further execution on error
    }
}

// Define the number of results per page
$results_per_page = 10;

// Fetch the current page number from the URL, default is 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Calculate the starting row of the results
$start_from = ($page - 1) * $results_per_page;

// Proceed to fetch and display departments
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
                                <h4 class="card-title float-left mt-2">Département</h4>
                                <a href="ajouter_departement.php" class="btn btn-primary float-right veiwbutton">Ajouter
                                    Département</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form method="get">
                            <div class="row formtype">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nom de Département</label>
                                        <input type="text" name="search" class="form-control"
                                            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success btn-block mt-0 search_button"
                                            id="rechercher">Rechercher</button>
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
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nom Département</th>
                                                <th>Entreprise</th>
                                                <th>Description</th>
                                                <th class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            try {
                                                // Get the total number of departments for pagination
                                                $search = isset($_GET['search']) ? $_GET['search'] : '';
                                                $total_sql = "SELECT COUNT(*) FROM Département WHERE Nom_Département LIKE :search";
                                                $total_stmt = $pdo->prepare($total_sql);
                                                $total_stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
                                                $total_stmt->execute();
                                                $total_departments = $total_stmt->fetchColumn();

                                                // Fetch departments for the current page
                                                $sql = "SELECT * FROM Département WHERE Nom_Département LIKE :search LIMIT :start_from, :results_per_page";
                                                $stmt = $pdo->prepare($sql);
                                                $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
                                                $stmt->bindValue(':start_from', $start_from, PDO::PARAM_INT);
                                                $stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
                                                $stmt->execute();

                                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<tr>";
                                                    echo "<td>" . htmlspecialchars($row['DépartementID']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['Nom_Département']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['Entreprise']) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['Description']) . "</td>";
                                                    echo '<td class="text-right">
                                                            <div class="dropdown">
                                                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton' . $row['DépartementID'] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    Actions
                                                                </button>
                                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $row['DépartementID'] . '">
                                                                    <a class="dropdown-item" href="modifier_departement.php?id=' . $row['DépartementID'] . '">
                                                                        <i class="fas fa-edit mr-2"></i>Modifier
                                                                    </a>
                                                                    <a class="dropdown-item delete" href="#" data-id="' . $row['DépartementID'] . '">
                                                                        <i class="fas fa-trash-alt mr-2"></i>Supprimer
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>';
                                                    echo "</tr>";
                                                }
                                            } catch (PDOException $e) {
                                                echo "Error: " . $e->getMessage();
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php
                                // Calculate the total number of pages
                                $total_pages = ceil($total_departments / $results_per_page);

                                // Display pagination
                                if ($total_pages > 1) {
                                    echo '<div class="pagination">';
                                    if ($page > 1) {
                                        echo '<a href="?page=' . ($page - 1) . '&search=' . $search . '">Précédent</a>';
                                    }

                                    for ($i = 1; $i <= $total_pages; $i++) {
                                        echo '<a href="?page=' . $i . '&search=' . $search . '" class="' . ($i == $page ? 'active' : '') . '">' . $i . '</a>';
                                    }

                                    if ($page < $total_pages) {
                                        echo '<a href="?page=' . ($page + 1) . '&search=' . $search . '">Suivant</a>';
                                    }
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirmation de suppression</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer ce département ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Supprimer</button>
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

    <script>
        $(document).ready(function () {
            // Handle delete click
            var departementId;
            $('.delete').on('click', function (e) {
                e.preventDefault();
                departementId = $(this).data('id');

                // Show Bootstrap modal for confirmation
                $('#deleteConfirmationModal').modal('show');
            });

            // Handle confirm delete
            $('#confirmDelete').on('click', function () {
                $.ajax({
                    url: 'list_departement.php',
                    type: 'GET',
                    data: {
                        delete_id: departementId
                    },
                    success: function () {
                        $('#deleteConfirmationModal').modal('hide');
                        location.reload();
                    },
                    error: function () {
                        alert('Erreur lors de la suppression du département.');
                    }
                });
            });
        });
    </script>

</body>

</html>
