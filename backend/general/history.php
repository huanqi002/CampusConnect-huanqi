<?php
// Shared helpers for the "My sessions" dashboard and full history views.
// Expects $conn already created (config.php included by the page)

function fetchMyActivity(mysqli $conn, int $myId): mysqli_result
{
    $sql = "SELECT sr.id AS request_id, sr.subject, sr.status AS request_status,
                   sr.student_id, sr.volunteer_id,
                   us.name AS student_name, uv.name AS volunteer_name,
                   s.id AS session_id, s.session_date, s.session_time, s.mode, s.status AS session_status,
                   f.id AS feedback_id, f.rating
            FROM support_requests sr
            JOIN users us ON sr.student_id = us.id
            JOIN users uv ON sr.volunteer_id = uv.id
            LEFT JOIN sessions s ON s.id = (SELECT id FROM sessions WHERE request_id = sr.id ORDER BY id DESC LIMIT 1)
            LEFT JOIN feedback f ON f.session_id = s.id
            WHERE sr.student_id = ? OR sr.volunteer_id = ?
            ORDER BY sr.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $myId, $myId);
    $stmt->execute();
    return $stmt->get_result();
}

function fetchFullHistory(mysqli $conn, int $myId): mysqli_result
{
    $sql = "SELECT sr.subject, sr.status AS request_status,
                   us.name AS student_name, uv.name AS volunteer_name,
                   s.session_date, s.session_time, s.mode, s.status AS session_status,
                   f.rating, f.comments
            FROM support_requests sr
            JOIN users us ON sr.student_id = us.id
            JOIN users uv ON sr.volunteer_id = uv.id
            LEFT JOIN sessions s ON s.request_id = sr.id
            LEFT JOIN feedback f ON f.session_id = s.id
            WHERE sr.student_id = ? OR sr.volunteer_id = ?
            ORDER BY s.session_date DESC, s.session_time DESC, sr.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $myId, $myId);
    $stmt->execute();
    return $stmt->get_result();
}
