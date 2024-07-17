<?php


require '../db_config.php';
include('../return_to_login_page.php');

// Check if the user is logged in
if (!isset($_SESSION['authentification_id']) || !isset($_SESSION['role']) || !isset($_SESSION['EmployeID'])) {
    // Redirect to login page if not logged in
    header('Location: ../login_process.php');
    exit(); // Ensure that script stops here to prevent further execution
}

$employe_id = $_SESSION['EmployeID'];

// Fetch main objectives assigned to the logged-in employee
$sql = "SELECT * FROM Objectifs WHERE EmployeID = :employe_id AND ParentObjectifID IS NULL AND Statut != 'Terminé'";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':employe_id', $employe_id, PDO::PARAM_INT);
$stmt->execute();
$main_objectives = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch sub-objectives for each main objective
foreach ($main_objectives as &$main_objective) {
    $sql = "SELECT * FROM Objectifs WHERE ParentObjectifID = :parent_objectif_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':parent_objectif_id', $main_objective['ObjectifID'], PDO::PARAM_INT);
    $stmt->execute();
    $main_objective['sub_objectives'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
unset($main_objective);

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['objectif_id']) && isset($_POST['status'])) {
    $objectif_id = $_POST['objectif_id'];
    $status = $_POST['status'];
    $sql = "UPDATE Objectifs SET Statut = :status WHERE ObjectifID = :objectif_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':objectif_id', $objectif_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect to avoid form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Update status based on the due date
$current_date = date("Y-m-d");
foreach ($main_objectives as &$main_objective) {
    if ($main_objective['DateEcheance'] < $current_date && $main_objective['Statut'] != 'Terminé') {
        $main_objective['Statut'] = 'Durée atteinte objectif non atteint';
        $sql = "UPDATE Objectifs SET Statut = 'Durée atteinte objectif non atteint' WHERE ObjectifID = :objectif_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':objectif_id', $main_objective['ObjectifID'], PDO::PARAM_INT);
        $stmt->execute();
    }
    foreach ($main_objective['sub_objectives'] as &$sub_objective) {
        // Check if sub-objective status is updated
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['objectif_id']) && $_POST['objectif_id'] == $sub_objective['ObjectifID']) {
            $sub_status = $_POST['status'];
            $sql = "UPDATE Objectifs SET Statut = :status WHERE ObjectifID = :objectif_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':status', $sub_status, PDO::PARAM_STR);
            $stmt->bindParam(':objectif_id', $sub_objective['ObjectifID'], PDO::PARAM_INT);
            $stmt->execute();
        }
        // Check if sub-objective due date is passed
        if ($sub_objective['DateEcheance'] < $current_date && $sub_objective['Statut'] != 'Terminé') {
            $sub_objective['Statut'] = 'Durée atteinte objectif non atteint';
            $sql = "UPDATE Objectifs SET Statut = 'Durée atteinte objectif non atteint' WHERE ObjectifID = :objectif_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':objectif_id', $sub_objective['ObjectifID'], PDO::PARAM_INT);
            $stmt->execute();
        }
    }
}
unset($main_objective);
unset($sub_objective);
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
        .page-wrapper {
            margin-top: 80px;
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
                            <h4 class="card-title float-left mt-2">Voir Objectifs</h4>
                        </div>
                    </div>
                </div>

                <div class="container mt-5">
                    <h2 class="mb-4">Liste des Objectifs</h2>
                    <?php foreach ($main_objectives as $main_objective) : ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($main_objective['Titre']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($main_objective['Description']); ?></p>
                                <p class="card-text"><strong>Date d'échéance:</strong> <?php echo htmlspecialchars($main_objective['DateEcheance']); ?></p>
                                <p class="card-text"><strong>Statut:</strong> <?php echo htmlspecialchars($main_objective['Statut']); ?></p>
                                <form method="POST" action="">
                                    <input type="hidden" name="objectif_id" value="<?php echo $main_objective['ObjectifID']; ?>">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="submit" name="status" value="En cours" class="btn btn-warning">En cours</button>
                                        <button type="submit" name="status" value="Terminé" class="btn btn-success">Terminé</button>
                                    </div>
                                </form>
                                <?php if (!empty($main_objective['sub_objectives'])): ?>
                                    <div class="mt-3">
                                        <h6>Sous-objectifs:</h6>
                                        <ul>
                                            <?php foreach ($main_objective['sub_objectives'] as $sub_objective): ?>
                                                <li>
                                                    <strong><?php echo htmlspecialchars($sub_objective['Titre']); ?></strong>
                                                    <form method="POST" action="" style="display: inline;">
                                                        <input type="hidden" name="objectif_id" value="<?php echo $sub_objective['ObjectifID']; ?>">
                                                        <div class="btn-group" role="group" aria-label="Basic example">
                                                            <button type="submit" name="status" value="En cours" class="btn btn-warning btn-sm">En cours</button>
                                                            <button type="submit" name="status" value="Terminé" class="btn btn-success btn-sm">Terminé</button>
                                                        </div>
                                                        <span class="ml-2"><?php echo htmlspecialchars($sub_objective['Statut']); ?></span>
                                                    </form>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($main_objectives)) : ?>
                        <p class="text-center">Aucun objectif disponible.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>
