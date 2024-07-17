<?php
// Ensure session is started at the very beginning
session_start();
require '../db_config.php';

// Check if the user is logged in
if (!isset($_SESSION['authentification_id']) || !isset($_SESSION['role'])) {
    // Redirect to login page if not logged in
    header('Location: ../login_process.php');
    exit(); // Ensure that script stops here to prevent further execution
}

// Fetch employees from the database
$sql = "SELECT EmployéID, NomPrenom FROM Employé";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $main_objectif = $_POST['main_objectif'];
    $sub_objectifs = $_POST['sub_objectif'];
    $dateEcheance = $_POST['dateEcheance'];
    $employe_id = $_POST['employe'];

    // Insert main objective
    $sql = "INSERT INTO Objectifs (Titre, Description, EmployeID, DateEcheance, DateCreation, Type, Statut) VALUES (:titre, :description, :employe_id, :dateEcheance, NOW(), 'Principal', 'En attente')";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':titre', $titre);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':employe_id', $employe_id);
    $stmt->bindParam(':dateEcheance', $dateEcheance);
    $stmt->execute();
    $parent_objectif_id = $pdo->lastInsertId();

    // Insert sub-objectives
    foreach ($sub_objectifs as $sub_objectif) {
        if (!empty($sub_objectif)) {
            $sql = "INSERT INTO Objectifs (Titre, ParentObjectifID, EmployeID, DateEcheance, DateCreation, Type, Statut) VALUES (:titre, :parent_objectif_id, :employe_id, :dateEcheance, NOW(), 'Sous-objectif', 'En attente')";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':titre', $sub_objectif);
            $stmt->bindParam(':parent_objectif_id', $parent_objectif_id);
            $stmt->bindParam(':employe_id', $employe_id);
            $stmt->bindParam(':dateEcheance', $dateEcheance);
            $stmt->execute();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Ajouter Objectif</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/hrLogo.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/feathericon.min.css">
    <link rel="stylesheet" href="../assets/plugins/morris/morris.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/stylee.css">
    <style>
        .sub-objectifs-container {
            margin-top: 10px;
        }

        .sub-objectif-input {
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <?php include('header.php'); ?>
        <?php include('sidebar.php'); ?>

        <!-- Page Content Section -->
        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title mt-5">Ajouter un Objectif</h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form id="ajouterObjectifForm" method="POST" action="">
                            <div class="form-group">
                                <label for="titre">Titre</label>
                                <input type="text" class="form-control" id="titre" name="titre" required>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="main_objectif">Objectif Principal</label>
                                <input type="text" class="form-control" id="main_objectif" name="main_objectif" required>
                            </div>

                            <div class="form-group">
                                <label for="sub_objectifs">Sous-objectifs (séparés par des virgules)</label>
                                <div class="sub-objectifs-container" id="subObjectifsContainer">
                                    <input type="text" class="form-control sub-objectif-input" name="sub_objectif[]" placeholder="Ex: Sous-objectif 1">
                                </div>
                                <button type="button" class="btn btn-sm btn-secondary mt-2" id="addSubObjectifBtn">Ajouter Sous-objectif</button>
                            </div>

                            <div class="form-group">
                                <label for="dateEcheance">Date d'échéance</label>
                                <input type="date" class="form-control" id="dateEcheance" name="dateEcheance" required>
                            </div>

                            <div class="form-group">
                                <label for="employe">Employé Assigné</label>
                                <select class="form-control" id="employe" name="employe" required>
                                    <option value="">Sélectionner un employé</option>
                                    <?php foreach ($employees as $employee): ?>
                                        <option value="<?= htmlspecialchars($employee['EmployéID']) ?>"><?= htmlspecialchars($employee['NomPrenom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Ajouter Objectif</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Add sub-objectif input fields dynamically
            $('#addSubObjectifBtn').on('click', function() {
                $('<input type="text" class="form-control sub-objectif-input" name="sub_objectif[]" placeholder="Ex: Sous-objectif">').appendTo('#subObjectifsContainer');
            });
        });
    </script>
</body>

</html>
