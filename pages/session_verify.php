<?php
session_start();

// Check if $_SESSION['ID'] is not set
if (!isset($_SESSION['ID'])) {
    header("Location: /index.php");
    exit();
}