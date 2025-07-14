<?php
session_start();

if (empty($_SESSION['membre'])) {
    header('Location: pages/login.php');
    exit;
}
?>