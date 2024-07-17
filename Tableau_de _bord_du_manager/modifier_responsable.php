<?php
require '../db_config.php';
include('../return_to_login_page.php');

$id = $_GET['id'] ?? '';

// Fetch manager's data based on ID
if (!empty($id)) {
    $sql = "SELECT RHManagerID AS id, Nom_utilisateur AS utilisateur_nom, NomPrenom AS nom_prenom, Email AS email, Telephone AS telephone, Date_Embauche AS date_embauche, Role AS role FROM RHManager WHERE RHManagerID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $manager = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$manager) {
        // Redirect if manager with ID not found
        header("Location: list_responsable.php");
        exit();
    }

    // Assign fetched values to variables
    $utilisateur_nom = $manager['utilisateur_nom'];
    $nom_prenom = $manager['nom_prenom'];
    $email = $manager['email'];
    $telephone = $manager['telephone'];
    $date_embauche = $manager['date_embauche'];
    $role = $manager['role'];
} else {
    // Redirect if no ID provided
    header("Location: list_responsable.php");
    exit();
}

// Handle form submission for updating data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $utilisateur_nom = $_POST['utilisateur_nom'] ?? '';
    $nom_prenom = $_POST['nom_prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $date_embauche = $_POST['date_embauche'] ?? '';
    $role = $_POST['role'] ?? '';

    // Update data in the database
    $sql = "UPDATE RHManager SET Nom_utilisateur = :utilisateur_nom, NomPrenom = :nom_prenom, Email = :email, Telephone = :telephone, Date_Embauche = :date_embauche, Role = :role WHERE RHManagerID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'utilisateur_nom' => $utilisateur_nom,
        'nom_prenom' => $nom_prenom,
        'email' => $email,
        'telephone' => $telephone,
        'date_embauche' => $date_embauche,
        'role' => $role,
        'id' => $id
    ]);

    // Redirect back to list_responsable.php after update
    header("Location: list_responsable.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Modifier Responsable</title>
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/hrLogo.png">
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
                            <h3 class="page-title mt-5">Modifier un responsable</h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form method="POST">
                            <div class="row formtype">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nom d'utilisateur</label>
                                        <input class="form-control" type="text" name="utilisateur_nom" value="<?php echo htmlspecialchars($utilisateur_nom); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nom et Prénom</label>
                                        <input class="form-control" type="text" name="nom_prenom" value="<?php echo htmlspecialchars($nom_prenom); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Téléphone</label>
                                        <input class="form-control" type="text" name="telephone" value="<?php echo htmlspecialchars($telephone); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date d'Embauche</label>
                                        <div class="cal-icon">
                                            <input type="text" class="form-control datetimepicker" name="date_embauche" value="<?php echo htmlspecialchars($date_embauche); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Rôle</label>
                                        <select class="form-control" name="role" required>
                                            <option value="Employé" <?php echo ($role == 'Employé') ? 'selected' : ''; ?>>Employé</option>
                                            <option value="HR Manager" <?php echo ($role == 'HR Manager') ? 'selected' : ''; ?>>HR Manager</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary buttonedit">Enregistrer les modifications</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/jquery-3.5.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/moment.min.js"></script>
    <script src="../assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>
