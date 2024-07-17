<?php
require '../db_config.php';
include('../return_to_login_page.php');
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch department details
    $sql = "SELECT * FROM Département WHERE DépartementID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $department = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    // Redirect if ID not provided
    header('Location: list_departement.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission to update department
    $nom_departement = $_POST['nom_departement'];
    $entreprise = $_POST['entreprise'];
    $description = $_POST['description'];

    $sqlUpdate = "UPDATE Département SET Nom_Département = :nom, Entreprise = :entreprise, Description = :description WHERE DépartementID = :id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindValue(':nom', $nom_departement, PDO::PARAM_STR);
    $stmtUpdate->bindValue(':entreprise', $entreprise, PDO::PARAM_STR);
    $stmtUpdate->bindValue(':description', $description, PDO::PARAM_STR);
    $stmtUpdate->bindValue(':id', $id, PDO::PARAM_INT);
    if ($stmtUpdate->execute()) {
        $_SESSION['success'] = "Département mis à jour avec succès.";
        header('Location: list_departement.php');
        exit();
    } else {
        echo "Erreur lors de la mise à jour du département.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Département</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/feathericon.min.css">
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
                            <h3 class="page-title mt-5">Modifier Département</h3>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <?php if (isset($_SESSION['success'])) : ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['success']; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="row formtype">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nom de Département</label>
                                        <input type="text" name="nom_departement" class="form-control" value="<?php echo htmlspecialchars($department['Nom_Département']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Entreprise</label>
                                        <input type="text" name="entreprise" class="form-control" value="<?php echo htmlspecialchars($department['Entreprise']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" required><?php echo htmlspecialchars($department['Description']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Mettre à jour le Département</button>
                        </form>

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
