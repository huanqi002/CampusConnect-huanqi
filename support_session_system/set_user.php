<?php
require 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT id, name, role FROM users WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header('Location: select_user.php');
    exit;
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['role']    = $user['role'];
$_SESSION['name']    = $user['name'];

header('Location: index.php');
exit;
