<?php
session_start();
include('db_config.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Call the stored procedure
    $query = "CALL loginUser(:username)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $auth_id = $row['AuthentificationID'];
        $user_id = $row['UserID'];
        $full_name = $row['FullName'];
        $stored_role = $row['Role'];
        $stored_password_hash = $row['PasswordHash'];

        // Verify hashed password
        if (password_verify($password, $stored_password_hash)) {
            $_SESSION['authentification_id'] = $auth_id;
            $_SESSION['role'] = $stored_role;
            $_SESSION['full_name'] = $full_name;
            $_SESSION['UserID'] = $user_id;

            // Set EmployeID session variable if the user is an employee
            if ($stored_role == 'Employé') {
                $_SESSION['EmployeID'] = $user_id;
            }

            // Redirect based on role
            if ($stored_role == 'RH Manager') {
                header('Location: Tableau_de _bord_du_manager/manager.php');
                exit;
            } elseif ($stored_role == 'Employé') {
                header('Location: Tableau_de _bord_du_employe/employe.php');
                exit;
            } else {
                // Handle other roles or unexpected cases
                $error = 'Invalid role'; // Example error message
            }
        } else {
            // Invalid password
            $error = 'Invalid username or password';
        }
    } else {
        // Invalid username
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/stylee.css">
    <link rel="stylesheet" href="../assets/css/stylee.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom styles for this page */
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrap {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }

        .login-wrap .icon {
            font-size: 60px;
            color: #007bff;
        }

        .login-wrap h3 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .login-form .form-group {
            margin-bottom: 20px;
        }

        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
        }

        .login-form .submit {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            padding: 12px;
        }

        .login-form .submit:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-bottom: 20px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 10px;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-wrap">
            <div class="icon">
                <span class="fas fa-user-circle fa-3x"></span>
            </div>
            <h3>Se connecter</h3>

            <!-- Display error message if any -->
            <?php if ($error) : ?>
                <div class="alert"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Login form -->
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="login-form">
                <div class="form-group">
                    <input type="text" name="username" class="form-control" placeholder="Utilisateur" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Mot De Passe" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="form-control btn btn-primary submit">Connexion <span class="fas fa-sign-in-alt"></span></button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>