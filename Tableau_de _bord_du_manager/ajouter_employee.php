<?php
// Ensure session is started at the very beginning
require '../db_config.php';
include('../return_to_login_page.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form data
    $NomPrenom = $_POST['NomPrenom'];
    $Nom_utilisateur = $_POST['Nom_utilisateur'];
    $Mot_De_Passe = password_hash($_POST['Mot_De_Passe'], PASSWORD_DEFAULT); // Securely hash the password
    $Email = $_POST['Email'];
    $Adresse = $_POST['Adresse'];
    $Téléphone = $_POST['Téléphone'];
    $Date_Embauche = $_POST['Date_Embauche'];
    $DépartementID = $_POST['DépartementID'];
    $Role = $_POST['Role'];

    try {
        // Begin a transaction
        $pdo->beginTransaction();

        // Insert into Authentification table
        $authentification_sql = "INSERT INTO Authentification (Identifiant, Mot_de_passe, Role) VALUES (?, ?, ?)";
        $auth_stmt = $pdo->prepare($authentification_sql);
        $auth_stmt->execute([$Nom_utilisateur, $Mot_De_Passe, $Role]); // Include Role in the insert

        // Get the last inserted id from Authentification table
        $AuthentificationID = $pdo->lastInsertId();

        // Insert into Employé table
        $employee_sql = "INSERT INTO Employé (NomPrenom, Nom_utilisateur, Mot_De_Passe, Email, Adresse, Téléphone, Date_Embauche, DépartementID, AuthentificationID, Role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $emp_stmt = $pdo->prepare($employee_sql);
        $emp_stmt->execute([$NomPrenom, $Nom_utilisateur, $Mot_De_Passe, $Email, $Adresse, $Téléphone, $Date_Embauche, $DépartementID, $AuthentificationID, $Role]);

        // Commit the transaction
        $pdo->commit();

        // Set success message and redirect
        $_SESSION['success'] = "Employé ajouté avec succès!";
        header("Location: ajouter_employee.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if something failed
        $pdo->rollBack();
        echo "Failed to add employee: " . $e->getMessage();
    }
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
                            <h3 class="page-title mt-5">Ajouter un Employé</h3>
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
                        <form action="ajouter_employee.php" method="POST">
                            <div class="row formtype">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nom et Prénom</label>
                                        <input class="form-control" type="text" name="NomPrenom" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nom d'utilisateur</label>
                                        <input class="form-control" type="text" name="Nom_utilisateur" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Mot De Passe</label>
                                        <input class="form-control" type="password" name="Mot_De_Passe" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input class="form-control" type="email" name="Email" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Adresse</label>
                                        <input class="form-control" type="text" name="Adresse" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Téléphone</label>
                                        <input class="form-control" type="text" name="Téléphone" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date d'Embauche</label>
                                        <input class="form-control" type="date" name="Date_Embauche" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Rôle</label>
                                        <select class="form-control" name="Role" required>
                                            <option value="">Sélectionner</option>
                                            
                                            <option>Employé</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Département</label>
                                        <select class="form-control" name="DépartementID" required>
                                            <option value="">Sélectionner</option>
                                            <?php
                                            $sql = "SELECT DépartementID, Nom_Département FROM Département";
                                            $result = $pdo->query($sql);
                                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                echo "<option value='{$row['DépartementID']}'>{$row['Nom_Département']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary buttonedit">Créer un employé</button>
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