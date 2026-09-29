<?php
// ===================================================================
// Database connection settings for XAMPP (default MySQL: root / no password)
// Change these three values if your XAMPP setup is different.
// ===================================================================
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'support_system';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

session_start();
?>
