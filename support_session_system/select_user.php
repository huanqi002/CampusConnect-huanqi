<?php
require 'config.php';
$result = $conn->query("SELECT id, name, role FROM users ORDER BY role, name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Support Session Manager - Select User</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topbar">
    <span class="brand">Support Session Manager</span>
</header>
<main class="page">
    <h1>Who are you?</h1>
    <p class="subtitle">Choose your name to continue. (In the full system this would be a login page.)</p>
    <div class="user-list">
        <?php while ($row = $result->fetch_assoc()): ?>
        <a class="user-pick" href="set_user.php?id=<?php echo (int)$row['id']; ?>">
            <span><?php echo htmlspecialchars($row['name']); ?></span>
            <span class="role-tag"><?php echo htmlspecialchars(ucfirst($row['role'])); ?></span>
        </a>
        <?php endwhile; ?>
    </div>
</main>
</body>
</html>
