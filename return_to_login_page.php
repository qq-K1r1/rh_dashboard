<?php
session_start();

if (!isset($_SESSION['authentification_id']) || !isset($_SESSION['role'])) {
    header('Location: ../login_process.php');
    exit();
}
?>