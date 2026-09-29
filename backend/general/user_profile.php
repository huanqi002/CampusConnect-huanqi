<?php
// Shared helpers for identifying, switching, and requiring the current user.
// Expects $conn and session already started (config.php included by the page)

function requireLogin(): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: ../user_management/select_user.php');
        exit;
    }
}

function currentUser(): array
{
    return [
        'id'   => $_SESSION['user_id'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'name' => $_SESSION['name'] ?? null,
    ];
}

function listUsers(mysqli $conn): mysqli_result
{
    return $conn->query("SELECT id, name, role FROM users ORDER BY role, name");
}

function findUserById(mysqli $conn, int $id): ?array
{
    $stmt = $conn->prepare("SELECT id, name, role FROM users WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

function loginAsUser(array $user): void
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['name']    = $user['name'];
}

function logoutUser(): void
{
    session_destroy();
}

function registerUser(mysqli $conn, string $name, string $role, string $email): array
{
    $stmt = $conn->prepare("INSERT INTO users (name, role, email) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $name, $role, $email);
    $stmt->execute();

    return ['id' => $conn->insert_id, 'name' => $name, 'role' => $role];
}
