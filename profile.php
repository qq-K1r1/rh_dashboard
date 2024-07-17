<?php
require 'db_config.php';
include('return_to_login_page.php');

$authentification_id = $_SESSION['authentification_id'];
$role = $_SESSION['role'];

// Fetch user details
if ($role === 'Employé') {
    $sql = "SELECT * FROM Employé WHERE AuthentificationID = :authentification_id";
} elseif ($role === 'RH Manager') {
    $sql = "SELECT * FROM RHManager WHERE AuthentificationID = :authentification_id";
} else {
    die("Unknown role");
}

$stmt = $pdo->prepare($sql);
$stmt->execute(['authentification_id' => $authentification_id]);
$user = $stmt->fetch();

// Default profile picture path
$profile_pic = !empty($user['profile_photo']) ? "Tableau_de _bord_du_employe/uploads/{$user['profile_photo']}" : "Tableau_de _bord_du_employe/uploads/default_pic.jpg";

// Function to safely get data or return empty string if null
function safeGet($array, $key) {
    return isset($array[$key]) ? htmlspecialchars($array[$key]) : '';
}

$userName = safeGet($user, 'NomPrenom');
$userRole = ($role === 'RH Manager') ? 'Manager' : 'Employé';
$userEmail = safeGet($user, 'Email');
$userPhone = safeGet($user, 'Téléphone');
$userAddress = safeGet($user, 'Adresse');
$userDOB = safeGet($user, 'Date_Embauche');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_details'])) {
        // Update personal details
        $name = $_POST['name'];
        $dob = $_POST['dob'];
        $email = $_POST['email'];
        $phone = $_POST['mobile'];
        $address = $_POST['address'];

        if ($role === 'Employé') {
            $updateSql = "UPDATE Employé SET NomPrenom = :name, Date_Embauche = :dob, Email = :email, Téléphone = :phone, Adresse = :address WHERE AuthentificationID = :authentification_id";
        } elseif ($role === 'RH Manager') {
            $updateSql = "UPDATE RHManager SET NomPrenom = :name, Date_Embauche = :dob, Email = :email, Téléphone = :phone, Adresse = :address WHERE AuthentificationID = :authentification_id";
        }

        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([
            'name' => $name,
            'dob' => $dob,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'authentification_id' => $authentification_id
        ]);

        // Refresh the page to show updated details
        header("Location: profile.php");
        exit;
    } elseif (isset($_POST['change_password'])) {
        // Change password
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            echo "Les mots de passe ne correspondent pas.";
        } else {
            $passwordSql = "SELECT mot_de_passe FROM Authentification WHERE AuthentificationID = :authentification_id";
            $passwordStmt = $pdo->prepare($passwordSql);
            $passwordStmt->execute(['authentification_id' => $authentification_id]);
            $auth = $passwordStmt->fetch();

            if (password_verify($currentPassword, $auth['mot_de_passe'])) {
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $updatePasswordSql = "UPDATE Authentification SET mot_de_passe = :new_password WHERE AuthentificationID = :authentification_id";
                $updatePasswordStmt = $pdo->prepare($updatePasswordSql);
                $updatePasswordStmt->execute([
                    'new_password' => $newPasswordHash,
                    'authentification_id' => $authentification_id
                ]);

                echo "Mot de passe changé avec succès.";
            } else {
                echo "Mot de passe actuel incorrect.";
            }
        }
    } elseif (isset($_POST['change_picture'])) {
        // Change profile picture
        if ($_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'Tableau_de _bord_du_employe/uploads/';
            $uploadFile = $uploadDir . basename($_FILES['profile_pic']['name']);

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadFile)) {
                $updatePictureSql = ($role === 'Employé') ? "UPDATE Employé SET profile_photo = :profile_photo WHERE AuthentificationID = :authentification_id" : "UPDATE RHManager SET profile_photo = :profile_photo WHERE AuthentificationID = :authentification_id";
                $updatePictureStmt = $pdo->prepare($updatePictureSql);
                $updatePictureStmt->execute([
                    'profile_photo' => basename($_FILES['profile_pic']['name']),
                    'authentification_id' => $authentification_id
                ]);

                // Refresh the page to show updated profile picture
                header("Location: profile.php");
                exit;
            } else {
                echo "Erreur lors du téléchargement de la photo de profil.";
            }
        } else {
            echo "Aucun fichier téléchargé.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Page de Profil</title>
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/hrLogo.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/feathericon.min.css">
    <link rel="stylesheet" href="assets/plugins/morris/morris.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/stylee.css">
</head>

<body>
    <div class="main-wrapper">
        <!-- Sidebar inclusion based on role -->
        <?php if ($role === 'Employé') { ?>
            <?php include('Tableau_de _bord_du_employe/sidebar.php'); ?>
        <?php } elseif ($role === 'RH Manager') { ?>
            <?php include('Tableau_de _bord_du_manager/sidebar.php'); ?>
        <?php } ?>

        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <?php include('header.php'); ?>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="profile-header">
                            <div class="row align-items-center">
                                <div class="col-auto profile-image">
                                    <a href="#">
                                        <img class="rounded-circle" alt="User Image" src="<?php echo htmlspecialchars($profile_pic); ?>">
                                    </a>
                                </div>
                                <div class="col ml-md-n2 profile-user-info">
                                    <h4 class="user-name mb-0"><?php echo $userName; ?></h4>
                                    <h6 class="text-muted"><?php echo $userRole; ?></h6>
                                    <div class="user-Location"><i class="fa fa-map-marker"></i> <?php echo $userAddress; ?></div>
                                </div>
                                <div class="col-auto profile-btn">
                                    <a href="profile.php" class="btn btn-primary">
                                        Modifier le Profil
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="profile-menu">
                            <ul class="nav nav-tabs nav-tabs-solid">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#per_details_tab">À propos</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#password_tab">Mot de passe</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#profile_pic_tab">Photo de profil</a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content profile-tab-cont">
                            <div class="tab-pane fade show active" id="per_details_tab">
                                <div class="row">
                                    <div class="col-lg-9">
                                        <div class="card">
                                            <div class="card-body">
                                                <h3 class="card-title d-flex justify-content-between">
                                                    Détails personnels
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editPersonalDetailsModal">
                                                        Modifier
                                                    </button>
                                                </h3>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><strong>Nom:</strong></label>
                                                            <p><?php echo $userName; ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><strong>Date de Naissance:</strong></label>
                                                            <p><?php echo $userDOB; ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><strong>Email:</strong></label>
                                                            <p><?php echo $userEmail; ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><strong>Téléphone:</strong></label>
                                                            <p><?php echo $userPhone; ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><strong>Adresse:</strong></label>
                                                            <p><?php echo $userAddress; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Change Password Tab -->
                            <div class="tab-pane fade" id="password_tab">
                                <div class="card">
                                    <div class="card-body">
                                        <form action="profile.php" method="POST">
                                            <h3 class="card-title">Changer le Mot de Passe</h3>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Mot de Passe Actuel</label>
                                                        <input type="password" class="form-control" name="current_password">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Nouveau Mot de Passe</label>
                                                        <input type="password" class="form-control" name="new_password">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Confirmer le Mot de Passe</label>
                                                        <input type="password" class="form-control" name="confirm_password">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" name="change_password" class="btn btn-primary">Changer le Mot de Passe</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Change Profile Picture Tab -->
                            <div class="tab-pane fade" id="profile_pic_tab">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Photo de Profil</h3>
                                        <form action="profile.php" method="POST" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label>Télécharger la Photo de Profil</label>
                                                <input type="file" class="form-control" name="profile_pic">
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" name="change_picture" class="btn btn-primary">Télécharger</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Personal Details Modal -->
                        <div class="modal fade" id="editPersonalDetailsModal" tabindex="-1" role="dialog" aria-labelledby="editPersonalDetailsModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editPersonalDetailsModalLabel">Modifier les Détails Personnels</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="profile.php" method="POST">
                                            <div class="form-group">
                                                <label>Nom</label>
                                                <input type="text" class="form-control" name="name" value="<?php echo $userName; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Date de Naissance</label>
                                                <input type="date" class="form-control" name="dob" value="<?php echo $userDOB; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" class="form-control" name="email" value="<?php echo $userEmail; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Mobile</label>
                                                <input type="text" class="form-control" name="mobile" value="<?php echo $userPhone; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Adresse</label>
                                                <input type="text" class="form-control" name="address" value="<?php echo $userAddress; ?>">
                                            </div>
                                            <div class="text-right">
                                                <button type="submit" name="update_details" class="btn btn-primary">Sauvegarder les Changements</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End of Edit Personal Details Modal -->

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery-3.5.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="assets/plugins/morris/morris.min.js"></script>
    <script src="assets/plugins/raphael/raphael.min.js"></script>
    <script src="assets/js/chart.morris.js"></script>
    <script src="assets/js/script.js"></script>
</body>

</html>
