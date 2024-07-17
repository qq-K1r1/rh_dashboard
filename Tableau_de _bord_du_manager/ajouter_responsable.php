<?php
// Ensure session is started at the very beginning
require '../db_config.php';
include('../return_to_login_page.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nomPrenom = $_POST['nom_prenom'];
    $nomUtilisateur = $_POST['nom_utilisateur'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $motDePasse = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $dateEmbauche = DateTime::createFromFormat('d/m/Y', $_POST['date_empauche'])->format('Y-m-d');

    try {

        $pdo->beginTransaction();

        $sqlAuth = "INSERT INTO Authentification (Identifiant, Mot_de_passe, Role) VALUES (:identifiant, :mot_de_passe, :role)";
        $stmtAuth = $pdo->prepare($sqlAuth);
        $stmtAuth->execute([
            ':identifiant' => $nomUtilisateur,
            ':mot_de_passe' => $motDePasse,
            ':role' => $role
        ]);
        $authentificationID = $pdo->lastInsertId();

        $sqlRH = "INSERT INTO RHManager (NomPrenom, Email, Telephone, Nom_utilisateur, Mot_De_Passe, Date_Embauche, AuthentificationID) VALUES (:nom_prenom, :email, :telephone, :nom_utilisateur, :mot_de_passe, :date_embauche, :auth_id)";
        $stmtRH = $pdo->prepare($sqlRH);
        $stmtRH->execute([
            ':nom_prenom' => $nomPrenom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':nom_utilisateur' => $nomUtilisateur,
            ':mot_de_passe' => $motDePasse,
            ':date_embauche' => $dateEmbauche,
            ':auth_id' => $authentificationID
        ]);

        $pdo->commit();

        $successMessage = "Responsable ajouté avec succès";
    } catch (PDOException $e) {

        $pdo->rollBack();
        $errorMessage = "Error: " . $e->getMessage();
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
    <link rel="stylesheet" href="https://cdn.oesmith.co.uk/morris-0.5.1.css">
    <link rel="stylesheet" href="../assets/plugins/morris/morris.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/stylee.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.fr.min.css">
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
                            <h3 class="page-title mt-5">Ajouter un responsable</h3>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?php if (isset($successMessage)): ?>
                            <div class="alert alert-success">
                                <?php echo $successMessage; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($errorMessage)): ?>
                            <div class="alert alert-danger">
                                <?php echo $errorMessage; ?>
                            </div>
                        <?php endif; ?>
                        <form method="post" action="">
                            <div class="row formtype">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nom Et Prénom</label>
                                        <input class="form-control" type="text" name="nom_prenom" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nom d'utilisateur</label>
                                        <input class="form-control" type="text" name="nom_utilisateur" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input class="form-control" type="email" name="email" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Mot de passe</label>
                                        <input class="form-control" type="password" name="mot_de_passe" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Téléphone</label>
                                        <input class="form-control" type="text" name="telephone" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date Embauche</label>
                                        <div class="cal-icon">
                                            <input type="text" class="form-control datepicker" name="date_empauche" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Role</label>
                                        <select class="form-control" name="role" required>
                                            <option value="">Sélectionner</option>
                                            <option>RH Manager</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary buttonedit">Créer un responsable</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.fr.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                language: 'fr',
                autoclose: true,
                todayHighlight: true
            });
        });
    </script>
</body>

</html>
```