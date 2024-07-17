<?php
// Include database configuration file
include('../db_config.php');

// Enable error reporting (for debugging purposes)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Redirect to login if session variables are not set
if (!isset($_SESSION['EmployeID'])) {
    header('Location: ../login_process.php');
    exit;
}

// Initialize error variable
$error = '';

// Retrieve EmployeID from session
$employeID = $_SESSION['EmployeID'];

// Fetch today's presence record if it exists
$dateToday = date('Y-m-d');
$query = "SELECT * FROM présence_journalière WHERE EmployéID = :employeID AND Date = :dateToday";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':employeID', $employeID, PDO::PARAM_INT);
$stmt->bindParam(':dateToday', $dateToday, PDO::PARAM_STR);
$stmt->execute();
$presenceRecord = $stmt->fetch(PDO::FETCH_ASSOC);

// Initialize variables for arrival and departure times
$arrivalTime = '';
$departureTime = '';

if ($presenceRecord) {
    // If presence record exists, populate arrival and departure times
    $arrivalTime = $presenceRecord['Heure_Arrivee'];
    $departureTime = $presenceRecord['Heure_Depart'];
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $arrivalTimePost = $_POST['arrivalTime'];
    $departureTimePost = $_POST['departureTime'];

    try {
        if ($presenceRecord) {
            // Update existing record
            $query = "UPDATE présence_journalière SET Heure_Arrivee = COALESCE(NULLIF(:arrivalTime, ''), Heure_Arrivee), 
                    Heure_Depart = COALESCE(NULLIF(:departureTime, ''), Heure_Depart)
                    WHERE EmployéID = :employeID AND Date = :dateToday";
        } else {
            // Insert new record
            $query = "INSERT INTO présence_journalière (Date, Heure_Arrivee, Heure_Depart, EmployéID) 
                    VALUES (:dateToday, :arrivalTime, :departureTime, :employeID)";
        }

        // Prepare and execute SQL query
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':employeID', $employeID, PDO::PARAM_INT);
        $stmt->bindParam(':dateToday', $dateToday, PDO::PARAM_STR);
        $stmt->bindParam(':arrivalTime', $arrivalTimePost, PDO::PARAM_STR);
        $stmt->bindParam(':departureTime', $departureTimePost, PDO::PARAM_STR);
        $stmt->execute();

        // Return success response
        echo json_encode(['status' => 'success']);
        exit; // End script execution after handling POST request
    } catch (Exception $e) {
        // Return error response
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit; // End script execution after handling POST request
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>HR Dashboard - Pointage Journalier</title>
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/hrLogo.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/stylee.css">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .card-body {
            padding: 30px;
        }

        .form-control {
            border-radius: 5px;
            padding: 15px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
        }

        #alert {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">

            <?php include('header.php'); ?>
            <?php include('sidebar.php'); ?>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="card mt-4">
                    <div class="card-header text-center">
                        <h2 class="mb-0">Pointage Journalier</h2>
                    </div>
                    <div class="card-body">
                        <form id="timeForm">
                            <div class="form-group">
                                <label for="nomPrenom">Nom et Prénom:</label>
                                <input type="text" id="nomPrenom" class="form-control" value="<?php echo $_SESSION['full_name']; ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="arrivalTime">Heure d'Arrivée:</label>
                                <input type="text" id="arrivalTime" class="form-control" name="arrivalTime" value="<?php echo $arrivalTime; ?>" readonly>
                                <button type="button" id="arrivalButton" class="btn btn-primary mt-2">Embaucher</button>
                            </div>
                            <div class="form-group">
                                <label for="departureTime">Heure de Départ:</label>
                                <input type="text" id="departureTime" class="form-control" name="departureTime" value="<?php echo $departureTime; ?>" readonly>
                                <button type="button" id="departureButton" class="btn btn-primary mt-2">Débaucher</button>
                            </div>
                            <button type="submit" id="sendButton" class="btn btn-success d-none">Envoyer</button>
                            <div id="alert"></div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../assets/js/jquery-3.5.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            var arrivalButton = $('#arrivalButton');
            var departureButton = $('#departureButton');
            var arrivalTimeInput = $('#arrivalTime');
            var departureTimeInput = $('#departureTime');
            var sendButton = $('#sendButton');
            var alertBox = $('#alert');

            // Click event handler for arrival button
            arrivalButton.on('click', function() {
                var currentTime = new Date().toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                arrivalTimeInput.val(currentTime); // Set arrival time dynamically
                arrivalTimeInput.prop('readonly', true);
                arrivalButton.prop('disabled', true);
                toggleSendButton(); // Enable send button
            });

            // Click event handler for departure button
            departureButton.on('click', function() {
                var currentTime = new Date().toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                departureTimeInput.val(currentTime); // Set departure time dynamically
                departureTimeInput.prop('readonly', true);
                departureButton.prop('disabled', true);
                toggleSendButton(); // Enable send button
            });

            // Function to toggle visibility of send button
            function toggleSendButton() {
                if (arrivalTimeInput.val() || departureTimeInput.val()) {
                    sendButton.removeClass('d-none');
                }
            }

            // Form submission handler
            $('#timeForm').on('submit', function(e) {
                e.preventDefault();

                // Retrieve form data
                var nomPrenom = $('#nomPrenom').val();
                var arrivalTime = arrivalTimeInput.val();
                var departureTime = departureTimeInput.val();

                // AJAX request to handle form submission
                $.ajax({
                    type: 'POST',
                    url: '', // Same PHP file
                    data: {
                        nomPrenom: nomPrenom,
                        arrivalTime: arrivalTime,
                        departureTime: departureTime
                    },
                    success: function(response) {
                        var res = JSON.parse(response);
                        if (res.status === 'success') {
                            alertBox.html('<div class="alert alert-success" role="alert">Enregistrement réussi!</div>');
                            // Update displayed times if successful
                            arrivalTimeInput.val(arrivalTime);
                            departureTimeInput.val(departureTime);
                        } else {
                            alertBox.html('<div class="alert alert-danger" role="alert">Erreur lors de l\'enregistrement: ' + res.message + '</div>');
                        }
                    },
                    error: function() {
                        alertBox.html('<div class="alert alert-danger" role="alert">Erreur lors de la communication avec le serveur.</div>');
                    }
                });
            });
        });
    </script>
</body>

</html>
