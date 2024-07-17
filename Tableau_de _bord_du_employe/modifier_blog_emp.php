<?php
require '../db_config.php';
include('../return_to_login_page.php');

if (isset($_GET['id'])) {
    $blogID = $_GET['id'];

    $sql = "SELECT * FROM Blog WHERE BlogID = :blogID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':blogID', $blogID, PDO::PARAM_INT);
    $stmt->execute();
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$blog) {
        echo '<div class="alert alert-danger" role="alert">Blog non trouvé.</div>';
        exit;
    }
} else {
    echo '<div class="alert alert-danger" role="alert">Aucun identifiant de blog fourni.</div>';
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_blog'])) {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $auteur = $_POST['auteur'];
    $photo = $blog['Photo'];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['photo']['name']);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
            $photo = $_FILES['photo']['name'];
        } else {
            echo '<div class="alert alert-danger" role="alert">Échec du téléchargement de la photo.</div>';
        }
    }

    $sql = "UPDATE Blog SET Titre = :titre, Description = :description, Auteur = :auteur, Photo = :photo WHERE BlogID = :blogID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':titre', $titre);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':auteur', $auteur);
    $stmt->bindParam(':photo', $photo);
    $stmt->bindParam(':blogID', $blogID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Blog mis à jour avec succès.';
        header("Location: mes_blogs_emp.php");
        exit;
    } else {
        echo '<div class="alert alert-danger" role="alert">Échec de la mise à jour du blog.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Système de Gestion des Ressources Humaines</title>
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/hrLogo.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/feathericon.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/stylee.css">
    <style>
        .form-control {
            border-radius: 0;
        }

        .current-photo {
            max-width: 100%;
            height: 300px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .custom-file-label::after {
            content: "Choisir fichier";
        }

        .buttonedit1 {
            border-radius: 20px;
            background-color: #007bff;
            border-color: #007bff;
        }

        .buttonedit1:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
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
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $blogID); ?>" method="POST" enctype="multipart/form-data">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Titre</label>
                                        <input class="form-control" type="text" name="titre" value="<?php echo htmlspecialchars($blog['Titre']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Upload Photo</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFile" name="photo">
                                            <label class="custom-file-label" for="customFile">Choisir fichier</label>
                                        </div>
                                        <?php if (!empty($blog['Photo'])): ?>
                                            <img src="uploads/<?php echo htmlspecialchars($blog['Photo']); ?>" alt="Current Photo" class="current-photo">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label>Auteur</label>
                                        <input class="form-control" type="text" name="auteur" value="<?php echo htmlspecialchars($blog['Auteur']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Description Du Blog</label>
                                        <textarea class="form-control" name="description" rows="6"><?php echo htmlspecialchars($blog['Description']); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary buttonedit1" name="update_blog">Publier</button>
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
    <script src="../assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
