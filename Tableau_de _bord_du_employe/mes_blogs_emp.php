<?php
// Ensure session is started at the very beginning
require '../db_config.php';
include('../return_to_login_page.php');
// Function to truncate text
function truncateText($text, $length = 100)
{
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length) . '...';
    }
    return htmlspecialchars($text);
}

$authentification_id = $_SESSION['authentification_id'];

// Initialize variables
$blogs = [];
$deleteMessage = '';

// Check if delete request is made
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_blog'])) {
    $blogIDToDelete = $_POST['delete_blog'];

    // Prepare and execute SQL statement to delete blog
    $sql = "DELETE FROM Blog WHERE BlogID = :blogID AND AuthentificationID = :authentification_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':blogID', $blogIDToDelete, PDO::PARAM_INT);
    $stmt->bindParam(':authentification_id', $authentification_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $deleteMessage = '<div class="alert alert-success" role="alert">Blog supprimé avec succès.</div>';
    } else {
        $deleteMessage = '<div class="alert alert-danger" role="alert">Erreur lors de la suppression du blog.</div>';
    }
}

// Prepare and execute SQL statement to fetch blogs
$sql = "SELECT BlogID, Titre, Description, Photo, Auteur FROM Blog WHERE AuthentificationID = :authentification_id ORDER BY DateCreation DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':authentification_id', $authentification_id, PDO::PARAM_INT);
$stmt->execute();
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <style>
        /* Custom Image Styles */
        .card-img-top {
            width: 100%;
            height: 200px;
            /* Adjust the height as needed */
            object-fit: cover;
            /* Ensures the image covers the area without distortion */
            border-radius: 4px;
            /* Optional: Adds a small border radius to the images */
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
                            <div class="mt-5">
                                <h4 class="card-title float-left mt-2">Blog</h4>
                                <a href="ajouter_blog.php" class="btn btn-primary float-right veiwbutton">Ajouter un Blog</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php
                    // Display delete message if there is one
                    if (!empty($deleteMessage)) {
                        echo '<div class="col-12">' . $deleteMessage . '</div>';
                    }

                    // Check if there are no blogs
                    if (empty($blogs)) {
                        echo '<div class="col-12">';
                        echo '<div class="alert alert-info" role="alert">Il n\'y a aucun blog à afficher pour le moment.</div>';
                        echo '</div>';
                    } else {
                        // Loop through fetched blogs and display them
                        foreach ($blogs as $blog) {
                            echo '<div class="col-12 col-sm-6 col-md-4">';
                            echo '<div class="card mb-4">';

                            // Check if the blog has a photo
                            if (!empty($blog['Photo']) && file_exists('../uploads/' . $blog['Photo'])) {
                                echo '<img class="card-img-top" src="../uploads/' . htmlspecialchars($blog['Photo']) . '" alt="Card image cap">';
                            } else {
                                // Default image if no photo exists
                                echo '<img class="card-img-top" src="../assets/img/default-blog.png" alt="Default Image">';
                            }

                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">' . htmlspecialchars($blog['Titre']) . '</h5>';

                            // Truncate description if it exceeds 100 characters
                            echo '<p class="card-text">' . truncateText($blog['Description'], 100) . '</p>';

                            // Button group
                            echo '<div class="btn-group" role="group" aria-label="Basic example">';
                            echo '<a href="#" class="btn btn-primary">Lire la suite</a>';
                            echo '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#confirmDeleteModal-' . $blog['BlogID'] . '">Supprimer</button>';
                            echo '<a href="modifier_blog.php?id=' . $blog['BlogID'] . '" class="btn btn-secondary">Modifier</a>'; // Modify button linked to modify_blog.php with BlogID as parameter
                            echo '</div>';

                            echo '</div>';
                            echo '</div>';
                            echo '</div>';

                            // Modal for confirmation
                            echo '<div class="modal fade" id="confirmDeleteModal-' . $blog['BlogID'] . '" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">';
                            echo '<div class="modal-dialog" role="document">';
                            echo '<div class="modal-content">';
                            echo '<div class="modal-header">';
                            echo '<h5 class="modal-title" id="confirmDeleteModalLabel">Confirmation de suppression</h5>';
                            echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                            echo '<span aria-hidden="true">&times;</span>';
                            echo '</button>';
                            echo '</div>';
                            echo '<div class="modal-body">';
                            echo '<p>Êtes-vous sûr de vouloir supprimer ce blog?</p>';
                            echo '</div>';
                            echo '<div class="modal-footer">';
                            echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>';
                            echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="POST" class="d-inline-block">';
                            echo '<input type="hidden" name="delete_blog" value="' . $blog['BlogID'] . '">';
                            echo '<button type="submit" class="btn btn-danger">Supprimer</button>';
                            echo '</form>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    ?>
                    <!-- End of PHP Code for Blog Cards -->
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