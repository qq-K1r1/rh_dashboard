<?php

include('../db_config.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if session variables are set
if (!isset($_SESSION['authentification_id']) || !isset($_SESSION['role'])) {
    die("Session not set.");
}

$authentification_id = $_SESSION['authentification_id'];
$role = $_SESSION['role'];

if ($role === 'Employé') {
    $sql = "SELECT NomPrenom FROM Employé WHERE AuthentificationID = :authentification_id";
} elseif ($role === 'RH Manager') {
    $sql = "SELECT NomPrenom FROM RHManager WHERE AuthentificationID = :authentification_id";
} else {
    die("Unknown role");
}

$stmt = $pdo->prepare($sql);
$stmt->execute(['authentification_id' => $authentification_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

$userName = htmlspecialchars($user['NomPrenom']);
$userRole = ($role === 'RH Manager') ? 'Manager' : 'Employee';

// Function to get profile photo path or default if not set
function getProfilePhotoPath($pdo, $role, $authentification_id)
{
    if ($role === 'Employé') {
        $sql = "SELECT profile_photo FROM Employé WHERE AuthentificationID = :authentification_id";
    } elseif ($role === 'RH Manager') {
        $sql = "SELECT profile_photo FROM RHManager WHERE AuthentificationID = :authentification_id";
    } else {
        return "uploads/default_pic.jpg"; // Default profile photo path
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['authentification_id' => $authentification_id]);
    $user = $stmt->fetch();

    if ($user && !empty($user['profile_photo'])) {
        return "uploads/{$user['profile_photo']}";
    } else {
        return "uploads/default_pic.jpg"; // Default profile photo path
    }
}

$profilePhotoPath = getProfilePhotoPath($pdo, $role, $authentification_id);
?>

<div class="header">
    <div class="header-left">
        <a href="employe.php" class="logo">
            <img src="../assets/img/hrLogo.png" width="50" height="70" alt="logo">
        </a>
        <a href="employe.php" class="logo logo-small">
            <img src="../assets/img/hrLogo.png" alt="Logo" width="30" height="30">
        </a>
    </div>
    <a href="javascript:void(0);" id="toggle_btn">
        <i class="fe fe-text-align-left"></i>
    </a>
    <a class="mobile_btn" id="mobile_btn">
        <i class="fas fa-bars"></i>
    </a>
    <ul class="nav user-menu">
        <li class="nav-item dropdown has-arrow">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                <span class="user-img">
                    <img class="rounded-circle" src="<?php echo $profilePhotoPath; ?>" width="31" alt="Profile Image">
                </span>
            </a>
            <div class="dropdown-menu">
                <div class="user-header">
                    <div class="avatar avatar-sm">
                        <img src="<?php echo $profilePhotoPath; ?>" alt="User Image" class="avatar-img rounded-circle">
                    </div>
                    <div class="user-text">
                        <h6><?php echo $userName; ?></h6>
                        <p class="text-muted mb-0"><?php echo $userRole; ?></p>
                    </div>
                </div>
                <a class="dropdown-item" href="../profile.php">Profile</a>
                <a class="dropdown-item" href="../logout.php">Déconnexion</a>
            </div>
        </li>
    </ul>
</div>