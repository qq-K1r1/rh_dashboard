<?php
require '../db_config.php';
include('../return_to_login_page.php');

// Include database configuration and connect to database
include('../db_config.php');

// Retrieve the employee ID from the URL parameter
$employee_id = $_GET['id'];

// Query to fetch employee details by ID
$sql = "SELECT e.EmployéID, e.NomPrenom, e.Nom_utilisateur, e.Email, e.Téléphone, e.Date_Embauche, d.Nom_Département, a.Role
        FROM Employé e
        LEFT JOIN Département d ON e.DépartementID = d.DépartementID
        LEFT JOIN Authentification a ON e.AuthentificationID = a.AuthentificationID
        WHERE e.EmployéID = :employee_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);
$stmt->execute();
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if employee record exists
if (!$employee) {
    // Redirect back to the list if no employee found
    header("Location: list_employee.php");
    exit;
}

// Handle form submission for updating employee information
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Retrieve and sanitize input data
        $nom_prenom = htmlspecialchars($_POST['nom_prenom']);
        $nom_utilisateur = htmlspecialchars($_POST['nom_utilisateur']);
        $email = htmlspecialchars($_POST['email']);
        $telephone = htmlspecialchars($_POST['telephone']);
        $date_embauche = htmlspecialchars($_POST['date_embauche']);
        $departement_id = $_POST['departement'];
        $role = htmlspecialchars($_POST['role']);

        // Update query
        $update_sql = "UPDATE Employé
                       SET NomPrenom = :nom_prenom, 
                           Nom_utilisateur = :nom_utilisateur,
                           Email = :email,
                           Téléphone = :telephone,
                           Date_Embauche = :date_embauche,
                           DépartementID = :departement_id
                       WHERE EmployéID = :employee_id";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->bindParam(':nom_prenom', $nom_prenom);
        $update_stmt->bindParam(':nom_utilisateur', $nom_utilisateur);
        $update_stmt->bindParam(':email', $email);
        $update_stmt->bindParam(':telephone', $telephone);
        $update_stmt->bindParam(':date_embauche', $date_embauche);
        $update_stmt->bindParam(':departement_id', $departement_id);
        $update_stmt->bindParam(':employee_id', $employee_id, PDO::PARAM_INT);

        // Execute the update statement
        if ($update_stmt->execute()) {
            // Set success message in session
            $_SESSION['success_message'] = "Employee information updated successfully.";
            // Redirect to the list page after successful update
            header("Location: modifier_employee.php");
            exit;
        } else {
            // Handle update error
            $error_message = "Error updating employee information";
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Modifier Employé</title>
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
                        <h4 class="card-title">Modifier Employé</h4>
                    </div>
                </div>
            </div>

            <!-- Display error message if set -->
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error_message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Display success message if set -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-12">
                    <form method="POST" action="modifier_employee.php?id=<?php echo $employee['EmployéID']; ?>">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nom_prenom">Nom et Prénom</label>
                                    <input type="text" class="form-control" id="nom_prenom" name="nom_prenom" value="<?php echo $employee['NomPrenom']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="nom_utilisateur">Nom d'utilisateur</label>
                                    <input type="text" class="form-control" id="nom_utilisateur" name="nom_utilisateur" value="<?php echo $employee['Nom_utilisateur']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $employee['Email']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="telephone">Téléphone</label>
                                    <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo $employee['Téléphone']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="date_embauche">Date Embauche</label>
                                    <input type="date" class="form-control" id="date_embauche" name="date_embauche" value="<?php echo $employee['Date_Embauche']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="departement">Département</label>
                                    <select class="form-control" id="departement" name="departement" required>
                                        <option value="">Sélectionner un département</option>
                                        <?php
                                        // Fetch departments from database
                                        $departments_sql = "SELECT DépartementID, Nom_Département FROM Département";
                                        $departments_result = $pdo->query($departments_sql);
                                        while ($dept = $departments_result->fetch(PDO::FETCH_ASSOC)) {
                                            $selected = ($dept['DépartementID'] == $employee['DépartementID']) ? 'selected' : '';
                                            echo "<option value='{$dept['DépartementID']}' $selected>{$dept['Nom_Département']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="role">Rôle</label>
                                    <input type="text" class="form-control" id="role" name="role" value="<?php echo $employee['Role']; ?>" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="../assets/js/jquery-3.5.1.min.js"></script>
<script src="../assets/js/popper.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/script.js"></script>

</body>

</html>
