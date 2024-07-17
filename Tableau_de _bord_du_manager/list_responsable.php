<?php
require '../db_config.php';
include('../return_to_login_page.php');

// Initialize variables for messages
$successMessage = '';
$errorMessage = '';

// Check if delete_id parameter is set in GET request
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Delete the manager from the RHManager table
        $sqlDeleteRHManager = "DELETE FROM RHManager WHERE RHManagerID = :id";
        $stmt = $pdo->prepare($sqlDeleteRHManager);
        $stmt->bindValue(':id', $deleteId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            // Delete the manager from the Authentification table
            $sqlDeleteAuth = "DELETE FROM Authentification WHERE AuthentificationID = :id";
            $stmt = $pdo->prepare($sqlDeleteAuth);
            $stmt->bindValue(':id', $deleteId, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $pdo->commit();
                $successMessage = "Manager deleted successfully.";
            } else {
                $pdo->rollBack();
                $errorMessage = "Error deleting manager from Authentification table.";
            }
        } else {
            $pdo->rollBack();
            $errorMessage = "Error deleting manager from RHManager table.";
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $errorMessage = "Error: " . $e->getMessage();
    }
}

// Fetch and display managers
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

try {
    if (!empty($searchTerm)) {
        $sql = "SELECT RHManagerID AS id, Nom_utilisateur AS utilisateur_nom, NomPrenom AS nom_prenom, Email AS email, Telephone AS telephone, Date_Embauche AS date_embauche, 'Role' AS role FROM RHManager WHERE RHManagerID LIKE :searchTerm OR Nom_utilisateur LIKE :searchTerm2 OR NomPrenom LIKE :searchTerm3";
        $stmt = $pdo->prepare($sql);
        $searchTermWildcard = '%' . $searchTerm . '%';
        $stmt->execute([
            'searchTerm' => $searchTermWildcard,
            'searchTerm2' => $searchTermWildcard,
            'searchTerm3' => $searchTermWildcard
        ]);
    } else {
        $sql = "SELECT RHManagerID AS id, Nom_utilisateur AS utilisateur_nom, NomPrenom AS nom_prenom, Email AS email, Telephone AS telephone, Date_Embauche AS date_embauche, 'Role' AS role FROM RHManager";
        $stmt = $pdo->query($sql);
    }

    $responsables = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = "Database error: " . $e->getMessage();
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
                                <h4 class="card-title float-left mt-2">List responsables</h4>
                                <a href="ajouter_responsable.php" class="btn btn-primary float-right veiwbutton">Ajouter responsable</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form method="GET" action="list_responsable.php">
                            <div class="row formtype">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Rechercher</label>
                                        <input type="text" class="form-control" name="search" placeholder="Rechercher par ID ou par nom" value="<?php echo htmlspecialchars($searchTerm); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-success btn-block mt-0">Rechercher</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Display success or error messages -->
                <?php if (!empty($successMessage)) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $successMessage; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errorMessage)) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $errorMessage; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="datatable table table-stripped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Utilisateur Nom</th>
                                                <th>Nom & Prenom</th>
                                                <th>Email</th>
                                                <th>Telephone</th>
                                                <th>Date Embauche</th>
                                                <th>Role</th>
                                                <th class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($responsables as $responsable) : ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($responsable['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($responsable['utilisateur_nom']); ?></td>
                                                    <td><?php echo htmlspecialchars($responsable['nom_prenom']); ?></td>
                                                    <td><?php echo htmlspecialchars($responsable['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($responsable['telephone']); ?></td>
                                                    <td><?php echo htmlspecialchars($responsable['date_embauche']); ?></td>
                                                    <td class="text-left"><?php echo htmlspecialchars($responsable['role']); ?></td>
                                                    <td class="text-right">
                                                        <div class="dropdown">
                                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton<?php echo htmlspecialchars($responsable['id']); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Actions
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo htmlspecialchars($responsable['id']); ?>">
                                                                <a class="dropdown-item" href="modifier_responsable.php?id=<?php echo htmlspecialchars($responsable['id']); ?>">
                                                                    <i class="fas fa-pencil-alt mr-2"></i>Modifier
                                                                </a>
                                                                <a class="dropdown-item delete" href="#" data-id="<?php echo htmlspecialchars($responsable['id']); ?>">
                                                                    <i class="fas fa-trash-alt mr-2"></i>Supprimer
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
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
                    Êtes-vous sûr de vouloir supprimer ce responsable ?
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
        $(document).ready(function() {
            // Delete confirmation
            $('.delete').click(function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#deleteConfirmationModal').modal('show');
                $('#confirmDelete').data('id', id);
            });

            $('#confirmDelete').click(function() {
                var id = $(this).data('id');
                $.ajax({
                    url: 'list_responsable.php?delete_id=' + id,
                    type: 'GET',
                    success: function(response) {
                        // Reload the page after successful deletion
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error deleting manager:', error);
                        alert('Error deleting manager. Please try again.');
                    }
                });
            });
        });
    </script>
</body>

</html>