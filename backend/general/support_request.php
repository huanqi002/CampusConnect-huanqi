<?php
// Shared helpers for the support_requests entity.
// Expects $conn already created (config.php included by the page)

function findSupportRequestById(mysqli $conn, int $id): ?array
{
    $stmt = $conn->prepare("SELECT sr.*, us.name AS student_name, uv.name AS volunteer_name
                             FROM support_requests sr
                             JOIN users us ON sr.student_id = us.id
                             JOIN users uv ON sr.volunteer_id = uv.id
                             WHERE sr.id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

function updateSupportRequestStatus(mysqli $conn, int $id, string $status): void
{
    $stmt = $conn->prepare("UPDATE support_requests SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
}
