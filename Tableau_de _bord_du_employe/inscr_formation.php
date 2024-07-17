<?php
// Ensure session is started at the very beginning
require '../db_config.php';
include('../return_to_login_page.php');

// Function to fetch all formations with employee registration status
function fetchFormationsWithEmployees($pdo, $employeID)
{
    $sql = "SELECT f.*, 
                   (SELECT ef.EmployeID FROM EmployeeFormation ef WHERE ef.FormationID = f.FormationID AND ef.EmployeID = :employeID) AS isRegistered
            FROM Formation f";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':employeID', $employeID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Check if action is 'register'
if (isset($_POST['action']) && $_POST['action'] == 'register') {
    // Check if EmployeID is set in the session
    if (!isset($_SESSION['EmployeID'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
        exit;
    }

    // Get EmployeID from session
    $employeID = $_SESSION['EmployeID'];

    // Sanitize formationID to prevent SQL injection
    $formationID = intval($_POST['formationID']);

    // Check if the employee is already registered for the formation
    $checkSql = "SELECT * FROM EmployeeFormation WHERE EmployeID = :employeID AND FormationID = :formationID";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->bindParam(':employeID', $employeID, PDO::PARAM_INT);
    $checkStmt->bindParam(':formationID', $formationID, PDO::PARAM_INT);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        // Employee is already registered, return an error
        echo json_encode(['status' => 'error', 'message' => 'Already registered for this formation']);
        exit;
    }

    // Prepare SQL statement to insert into EmployeeFormation table
    $sql = "INSERT INTO EmployeeFormation (EmployeID, FormationID) VALUES (:employeID, :formationID)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':employeID', $employeID, PDO::PARAM_INT);
    $stmt->bindParam(':formationID', $formationID, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
        // Registration successful, return success response
        echo json_encode(['status' => 'success']);
        exit;
    } else {
        // Registration failed
        echo json_encode(['status' => 'error', 'message' => 'Registration failed. Please try again.']);
        exit;
    }
}

// Check if action is 'cancel'
if (isset($_POST['action']) && $_POST['action'] == 'cancel') {
    // Check if EmployeID is set in the session
    if (!isset($_SESSION['EmployeID'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
        exit;
    }

    // Get EmployeID from session
    $employeID = $_SESSION['EmployeID'];

    // Sanitize formationID to prevent SQL injection
    $formationID = intval($_POST['formationID']);

    // Prepare SQL statement to delete from EmployeeFormation table
    $sql = "DELETE FROM EmployeeFormation WHERE EmployeID = :employeID AND FormationID = :formationID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':employeID', $employeID, PDO::PARAM_INT);
    $stmt->bindParam(':formationID', $formationID, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
        // Cancellation successful, return success response
        echo json_encode(['status' => 'success']);
        exit;
    } else {
        // Cancellation failed
        echo json_encode(['status' => 'error', 'message' => 'Cancellation failed. Please try again.']);
        exit;
    }
}

// Fetch all formations with the registration status for the logged-in employee
$employeID = $_SESSION['EmployeID'];
$formations = fetchFormationsWithEmployees($pdo, $employeID);
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
    <style>
        .card-body span {
            font-weight: bolder;
        }

        #success-message {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            width: 300px;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
    <?php include('header.php'); ?>
        <?php include('sidebar.php'); ?>

        <div class="page-wrapper">
            <div class="sidebar-wrapper">
                <div class="content container-fluid">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-sm-12 mt-5">
                                <h3 class="page-title mt-3">Liste des Formations Disponible</h3>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="manager.php">Tableau De Bord</a></li>
                                    <li class="breadcrumb-item active">Formations</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Formations Section -->
                    <div id="formations-list" class="row">
                        <?php foreach ($formations as $formation) : ?>
                            <div class="col-md-6">
                                <div class="card mb-3" id="formation-<?php echo $formation['FormationID']; ?>">
                                    <div class="card-body">
                                        <div>
                                            <h5><span>Nom de la Formation:</span> <?php echo htmlspecialchars($formation['Nom_Formation']); ?></h5>
                                            <p><span>Description :</span> <?php echo htmlspecialchars($formation['Description']); ?></p>
                                            <p><span>Date:</span> <?php echo htmlspecialchars($formation['Date_Formation']); ?></p>
                                            <?php
                                            // Check if the current employee is registered for this formation
                                            $isRegistered = $formation['isRegistered'] ? true : false;
                                            ?>
                                            <button class="btn btn-<?php echo $isRegistered ? 'danger' : 'primary'; ?>" data-action="<?php echo $isRegistered ? 'cancel' : 'register'; ?>" onclick="registerOrCancelFormation(<?php echo $formation['FormationID']; ?>, $(this))">
                                                <?php echo $isRegistered ? 'Annuler' : 'S\'inscrire'; ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- End of Formations Section -->

                </div>
            </div>
        </div>
    </div>

    <div id="success-message" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
        Action effectuée avec succès!
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <script src="../assets/js/jquery-3.5.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="../assets/plugins/raphael/raphael.min.js"></script>
    <script src="../assets/plugins/morris/morris.min.js"></script>
    <script src="../assets/js/chart.morris.js"></script>
    <script src="../assets/js/script.js"></script>
    <script>
      
    function registerOrCancelFormation(formationID, actionBtn) {
        var action = actionBtn.data('action');

        $.ajax({
            url: 'inscr_formation.php',
            type: 'POST',
            data: {
                action: action,
                formationID: formationID
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Toggle button text and action
                    if (action === 'register') {
                        actionBtn.data('action', 'cancel');
                        actionBtn.removeClass('btn-primary').addClass('btn-danger').text('Annuler');
                    } else if (action === 'cancel') {
                        actionBtn.data('action', 'register');
                        actionBtn.removeClass('btn-danger').addClass('btn-primary').text('S\'inscrire');
                    }

                    // Update the participants list
                    updateParticipantsList();

                    $('#success-message').fadeIn();
                    setTimeout(function() {
                        $('#success-message').fadeOut();
                    }, 3000); // Hide after 3 seconds
                } else {
                    console.error('Operation failed:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function updateParticipantsList() {
        // Perform AJAX request to fetch updated participants list
        $.ajax({
            url: 'fetch_participants.php', // Adjust the URL if needed
            type: 'GET', // Assuming you have a separate PHP script to fetch participants
            dataType: 'html', // Expecting HTML response for table update
            success: function(data) {
                $('#participantsList').html(data); // Update the table with new participant data
            },
            error: function(xhr, status, error) {
                console.error('Error fetching participants:', error);
            }
        });
    }
</script>

    </script>
</body>

</html>
