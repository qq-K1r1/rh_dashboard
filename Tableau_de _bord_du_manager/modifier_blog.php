<?php
require '../db_config.php';
include('../return_to_login_page.php');

// Check if BlogID is provided as a GET parameter
if (isset($_GET['id'])) {
    $blog_id = $_GET['id'];

    // Fetch blog details from the database
    $sql = "SELECT Titre, Description, Photo, Auteur FROM Blog WHERE BlogID = :blog_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':blog_id', $blog_id, PDO::PARAM_INT);
    $stmt->execute();
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if blog exists
    if (!$blog) {
        die("Blog not found.");
    }

    // Extract data from the fetched blog
    $titre = $blog['Titre'];
    $description = $blog['Description'];
    $photo = $blog['Photo'];
    $auteur = $blog['Auteur'];
} else {
    die("Blog ID not provided.");
}

// Function to update blog
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data and update the blog in the database
    $new_titre = $_POST['titre'];
    $new_description = $_POST['description'];
    $new_auteur = $_POST['auteur'];

    // Handle photo upload if a new file is selected
    if ($_FILES['filename']['size'] > 0) {
        // Check if a file is uploaded
        $target_dir = "uploads/"; // Directory where uploaded files will be saved
        $target_file = $target_dir . basename($_FILES['filename']['name']);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an actual image
        $check = getimagesize($_FILES['filename']['tmp_name']);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES['filename']['size'] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            // If everything is ok, try to upload file
            if (move_uploaded_file($_FILES['filename']['tmp_name'], $target_file)) {
                // File uploaded successfully, update database
                $sql_update = "UPDATE Blog SET Titre = :titre, Description = :description, Photo = :photo, Auteur = :auteur WHERE BlogID = :blog_id";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->bindValue(':titre', $new_titre, PDO::PARAM_STR);
                $stmt_update->bindValue(':description', $new_description, PDO::PARAM_STR);
                $stmt_update->bindValue(':photo', basename($_FILES['filename']['name']), PDO::PARAM_STR);
                $stmt_update->bindValue(':auteur', $new_auteur, PDO::PARAM_STR);
                $stmt_update->bindValue(':blog_id', $blog_id, PDO::PARAM_INT);
                $stmt_update->execute();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        // If no new file is uploaded, update without changing the existing photo
        $sql_update = "UPDATE Blog SET Titre = :titre, Description = :description, Auteur = :auteur WHERE BlogID = :blog_id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->bindValue(':titre', $new_titre, PDO::PARAM_STR);
        $stmt_update->bindValue(':description', $new_description, PDO::PARAM_STR);
        $stmt_update->bindValue(':auteur', $new_auteur, PDO::PARAM_STR);
        $stmt_update->bindValue(':blog_id', $blog_id, PDO::PARAM_INT);
        $stmt_update->execute();
    }

    // Redirect to mes_blogs.php after update
    header("Location: mes_blogs.php");
    exit();
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
                            <h3 class="page-title mt-2">Modifier Blog</h3>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-12">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row formtype">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Titre</label>
                                        <input class="form-control" type="text" name="titre" value="<?php echo htmlspecialchars($titre); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Upload Photo</label>
                                        <div class="custom-file mb-3">
                                            <input type="file" class="custom-file-input" id="customFile" name="filename">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Auteur</label>
                                        <select class="form-control" name="auteur">
                                            <option>oussama ahaddane</option>
                                            <!-- Add more options as needed -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Description Du Blog</label>
                                        <textarea class="form-control" name="description" cols="100" rows="16"><?php echo htmlspecialchars($description); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary buttonedit1 mt-4">Modifier</button>
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
