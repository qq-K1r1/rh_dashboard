<?php
require '../db_config.php';
include('../return_to_login_page.php');


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Escape user inputs for security
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $photo = $_FILES['photo']['name'];
    $auteur = $_SESSION['full_name']; // Use the full name from the session

    // Prepare an insert statement
    $sql = "INSERT INTO Blog (Titre, Description, Photo, Auteur, AuthentificationID) 
            VALUES (:titre, :description, :photo, :auteur, :authID)";

    if ($stmt = $pdo->prepare($sql)) {
        // Bind parameters
        $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':photo', $photo, PDO::PARAM_STR);
        $stmt->bindParam(':auteur', $auteur, PDO::PARAM_STR);
        $stmt->bindParam(':authID', $_SESSION['authentification_id'], PDO::PARAM_INT);

        // Upload file to server (adjust path as per your file storage strategy)
        $target_dir = "uploads/"; // Example directory for file uploads
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Blog post added successfully
            echo '<div class="alert alert-success mt-3" role="alert">Blog post added successfully.</div>';
            // Optionally redirect after successful insertion
            // header("Location: view_blogs.php");
            // exit();
        } else {
            // Error handling
            echo '<div class="alert alert-danger mt-3" role="alert">Error: Could not execute query.</div>';
        }
    } else {
        // Error handling
        echo '<div class="alert alert-danger mt-3" role="alert">Error: Could not prepare statement.</div>';
    }

    // Close statement
    unset($stmt);

    // Close connection
    unset($pdo);
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
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/stylee.css">
</head>
<body>
    <div class="main-wrapper">
        <?php include('header.php'); ?>
        <?php include('sidebar.php'); ?>

        <div class="page-wrapper">
            <div class="content mt-5">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title mt-2">Ajouter Blog</h3>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                            <div class="row formtype">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Titre</label>
                                        <input class="form-control" type="text" name="titre" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Upload Photo</label>
                                        <div class="custom-file mb-3">
                                            <input type="file" class="custom-file-input" id="customFile" name="photo" >
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Auteur</label>
                                        <input class="form-control" type="text" name="auteur" value="<?php echo htmlspecialchars($_SESSION['full_name']); ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Description Du Blog</label>
                                        <textarea class="form-control" name="description" rows="6" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary buttonedit1 mt-4">Publier</button>
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
    <script src="../assets/js/script.js"></script>
</body>
</html>
