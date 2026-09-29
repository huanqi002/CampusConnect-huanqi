<?php
require __DIR__ . '/../../general/config.php';
require __DIR__ . '/../../general/user_profile.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role  = $_POST['role'] ?? '';

    if ($name === '' || !in_array($role, ['student', 'volunteer'], true)) {
        $errors[] = 'Please enter your name and choose a role.';
    }

    if (!$errors) {
        $user = registerUser($conn, $name, $role, $email);
        loginAsUser($user);
        header('Location: ../support_request/index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Support Session Manager - Register</title>
<link rel="stylesheet" href="../../../frontend/css/style.css">
</head>
<body>
<header class="topbar">
    <span class="brand">Support Session Manager</span>
</header>
<main class="page">
    <h1>Register a new identity</h1>
    <p class="subtitle">(In the full system this would create a real account. Here it just adds a sample user.)</p>

    <?php foreach ($errors as $error): ?>
    <div class="message message-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endforeach; ?>

    <form method="post" action="register.php">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>

        <label for="email">Email (optional)</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

        <label>I am a</label>
        <div class="radio-group">
            <label><input type="radio" name="role" value="student" <?php echo (($_POST['role'] ?? '') === 'student') ? 'checked' : ''; ?> required> Student</label>
            <label><input type="radio" name="role" value="volunteer" <?php echo (($_POST['role'] ?? '') === 'volunteer') ? 'checked' : ''; ?>> Volunteer</label>
        </div>

        <div class="btn-row">
            <button type="submit" class="btn btn-primary">Register</button>
            <a class="btn btn-plain" href="select_user.php">Cancel</a>
        </div>
    </form>
</main>
</body>
</html>
