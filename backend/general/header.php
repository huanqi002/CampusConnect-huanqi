<?php
// Expects $conn and session already started (config.php included by the page)
$currentUserId = $_SESSION['user_id'] ?? null;
$currentRole   = $_SESSION['role'] ?? null;
$currentName   = $_SESSION['name'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Support Session Manager</title>
<link rel="stylesheet" href="../../../frontend/css/style.css">
</head>
<body>
<header class="topbar">
    <a href="index.php" class="brand">Support Session Manager</a>
    <?php if ($currentUserId): ?>
    <nav class="topnav">
        <span class="whoami">Signed in as <strong><?php echo htmlspecialchars($currentName); ?></strong> (<?php echo htmlspecialchars(ucfirst($currentRole)); ?>)</span>
        <a href="index.php">My sessions</a>
        <a href="history.php">History</a>
        <a href="logout.php">Switch user</a>
    </nav>
    <?php endif; ?>
</header>
<main class="page">
