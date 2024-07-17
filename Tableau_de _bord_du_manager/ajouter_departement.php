<?php
// Ensure session is started at the very beginning
require '../db_config.php';
include('../return_to_login_page.php');
// Initialize variables for messages
$successMessage = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $department_name = $_POST['department_name'];
    $company_name = $_POST['company_name'];
    $department_description = $_POST['department_description'];

    // Prepare the SQL statement
    $sql = "INSERT INTO département (Nom_Département, Entreprise, Description)
            VALUES (:department_name, :company_name, :department_description)";
    
    // Prepare the statement
    $stmt = $pdo->prepare($sql);

    // Bind the parameters
    $stmt->bindParam(':department_name', $department_name);
    $stmt->bindParam(':company_name', $company_name);
    $stmt->bindParam(':department_description', $department_description);

    // Execute the statement and set the appropriate message
    if ($stmt->execute()) {
        $successMessage = "Nouveau département ajouté avec succès";
    } else {
        $errorMessage = "Erreur: Impossible d'ajouter le département";
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
</head>

<body>
    <div class="main-wrapper">

        <div class="header">
            <div class="header-left">
                <a href="manager.php" class="logo"> 
                    <img src="../assets/img/hrLogo.png" width="50" height="70" alt="logo">
                </a>
                <a href="manager.php" class="logo logo-small"> 
                    <img src="../assets/img/hrLogo.png" alt="Logo" width="30" height="30">
                </a>
            </div>
            <a href="javascript:void(0);" id="toggle_btn"><i class="fe fe-text-align-left"></i></a>
            <a class="mobile_btn" id="mobile_btn"><i class="fas fa-bars"></i></a>
            <ul class="nav user-menu">
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown"> 
                        <span class="user-img"><img class="rounded-circle" src="../assets/img/profiles/pofile_avatar.jfif" width="31" alt="Soeng Souy"></span>
                    </a>
                    <div class="dropdown-menu">
                        <div class="user-header">
                            <div class="avatar avatar-sm">
                                <img src="../assets/img/profiles/pofile_avatar.jfif" alt="User Image" class="avatar-img rounded-circle">
                            </div>
                            <div class="user-text">
                                <h6>user</h6>
                                <p class="text-muted mb-0">role</p>
                            </div>
                        </div>
                        <a class="dropdown-item" href="profile.html">Profile</a>
                        <a class="dropdown-item" href="login.html">Déconnexion</a>
                    </div>
                </li>
            </ul>
        </div>

        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="active">
                            <a href="manager.php"><i class="fas fa-tachometer-alt"></i><span>Tableau De Bord</span></a>
                        </li>
                        <li class="list-divider"></li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-users-cog"></i> <span> Employés </span> <span class="menu-arrow"></span></a>
                            <ul class="submenu_class" style="display: none;">
                                <li><a href="list_employee.php">List employés</a></li>
                                <li><a href="ajouter_employee.php">Ajouter employé</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-building"></i> <span> Département </span><span class="menu-arrow"></span></a>
                            <ul class="submenu_class" style="display: none;">
                                <li><a href="list_departement.php">List Département</a></li>
                                <li><a href="ajouter_departement.php">ajouter Département</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-user"></i> <span>RH Responsable</span><span class="menu-arrow"></span></a>
                            <ul class="submenu_class" style="display: none;">
                                <li><a href="list_responsable.php">List Responsables</a></li>
                                <li><a href="ajouter_responsable.php">Ajouter Responsable</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-clock"></i> <span> Présence </span><span class="menu-arrow"></span></a>
                            <ul class="submenu_class" style="display: none;">
                                <li><a href="Presence_quotidienne.php">Présence Quotidienne</a></li>
                                <li><a href="toute_presence.php">Toute Présence</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="far fa-calendar-times"></i> <span> congé </span><span class="menu-arrow"></span></a>
                            <ul class="submenu_class" style="display: none;">
                                <li><a href="demande_de_conge.php">Les Demandes De Congé</a></li>
                                <li><a href="list_de_conge.php">List De Congé</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="calendar.html"><span>Calendar</span></a>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fe fe-table"></i> <span> Blog </span><span class="menu-arrow"></span></a>
                            <ul class="submenu_class" style="display: none;">
                                <li><a href="mes_blogs.php">Mes Blogs</a></li>
                                <li><a href="ajouter_blog.php">Ajouter Blog</a></li>
                                <li><a href="modifier_blog.php">Modifier Blog</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-chart-line"></i> <span> Les Reports </span><span class="menu-arrow"></span></a>
                            <ul class="submenu_class" style="display: none;">
                                <li><a href="expense-reports.html">Expense Report</a></li>
                                <li><a href="invoice-reports.html">Invoice Report</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="content mt-5">

                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title mt-2">Ajouter département</h3>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <?php if ($successMessage): ?>
                            <div class="alert alert-success">
                                <?php echo $successMessage; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($errorMessage): ?>
                            <div class="alert alert-danger">
                                <?php echo $errorMessage; ?>
                            </div>
                        <?php endif; ?>
                        <form action="ajouter_departement.php" method="POST">
                            <div class="row formtype">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Nom département</label>
                                        <input class="form-control" type="text" name="department_name" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Entreprise</label>
                                        <select class="form-control" id="sel1" name="company_name" required>
                                            <option value="">Sélectionner</option>
                                            <option value="Eureka Création">Eureka Création</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Description du département</label>
                                        <textarea cols="30" rows="6" class="form-control" name="department_description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary buttonedit1 mt-4">Ajouter département</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script data-cfasync="false" src="../../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
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
