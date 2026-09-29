<?php
// Shared helpers for the sessions, volunteer_availability, and feedback entities.
// Expects $conn already created (config.php included by the page)

function getAvailableTimes(mysqli $conn, int $volunteerId, string $date): array
{
    $stmt = $conn->prepare("SELECT available_time FROM volunteer_availability
                             WHERE volunteer_id = ? AND available_date = ? AND is_booked = 0
                             ORDER BY available_time");
    $stmt->bind_param('is', $volunteerId, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $times = [];
    while ($row = $result->fetch_assoc()) {
        $times[] = substr($row['available_time'], 0, 5); // HH:MM
    }
    return $times;
}

function findAvailableSlot(mysqli $conn, int $volunteerId, string $date, string $time): ?array
{
    $stmt = $conn->prepare("SELECT id FROM volunteer_availability
                             WHERE volunteer_id = ? AND available_date = ? AND available_time = ? AND is_booked = 0");
    $stmt->bind_param('iss', $volunteerId, $date, $time);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

function bookSlot(mysqli $conn, int $slotId): void
{
    $stmt = $conn->prepare("UPDATE volunteer_availability SET is_booked = 1 WHERE id = ?");
    $stmt->bind_param('i', $slotId);
    $stmt->execute();
}

function freeSlot(mysqli $conn, int $volunteerId, string $date, string $time): void
{
    $stmt = $conn->prepare("UPDATE volunteer_availability SET is_booked = 0
                             WHERE volunteer_id = ? AND available_date = ? AND available_time = ?");
    $stmt->bind_param('iss', $volunteerId, $date, $time);
    $stmt->execute();
}

function createSession(mysqli $conn, int $requestId, string $date, string $time, string $mode): void
{
    $stmt = $conn->prepare("INSERT INTO sessions (request_id, session_date, session_time, mode, status)
                             VALUES (?, ?, ?, ?, 'Scheduled')");
    $stmt->bind_param('isss', $requestId, $date, $time, $mode);
    $stmt->execute();
}

function findScheduledSession(mysqli $conn, int $sessionId): ?array
{
    $stmt = $conn->prepare("SELECT s.*, sr.student_id, sr.volunteer_id
                             FROM sessions s
                             JOIN support_requests sr ON s.request_id = sr.id
                             WHERE s.id = ? AND s.status = 'Scheduled'");
    $stmt->bind_param('i', $sessionId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

function findSessionForFeedback(mysqli $conn, int $sessionId): ?array
{
    $stmt = $conn->prepare("SELECT s.*, sr.student_id, sr.subject, uv.name AS volunteer_name
                             FROM sessions s
                             JOIN support_requests sr ON s.request_id = sr.id
                             JOIN users uv ON sr.volunteer_id = uv.id
                             WHERE s.id = ?");
    $stmt->bind_param('i', $sessionId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

function updateSessionStatus(mysqli $conn, int $sessionId, string $status): void
{
    $stmt = $conn->prepare("UPDATE sessions SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $sessionId);
    $stmt->execute();
}

function hasFeedback(mysqli $conn, int $sessionId): bool
{
    $stmt = $conn->prepare("SELECT id FROM feedback WHERE session_id = ?");
    $stmt->bind_param('i', $sessionId);
    $stmt->execute();
    return (bool) $stmt->get_result()->fetch_assoc();
}

function insertFeedback(mysqli $conn, int $sessionId, int $rating, string $comments): bool
{
    $stmt = $conn->prepare("INSERT INTO feedback (session_id, rating, comments) VALUES (?, ?, ?)");
    $stmt->bind_param('iis', $sessionId, $rating, $comments);
    return $stmt->execute();
}
