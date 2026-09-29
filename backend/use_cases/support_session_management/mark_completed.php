<?php
require __DIR__ . '/../../general/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'volunteer') {
    header('Location: index.php?err=' . urlencode('Only the volunteer can mark a session as completed.'));
    exit;
}

$sessionId = (int)($_POST['session_id'] ?? 0);

$stmt = $conn->prepare("SELECT s.id, s.request_id, sr.volunteer_id
                         FROM sessions s
                         JOIN support_requests sr ON s.request_id = sr.id
                         WHERE s.id = ? AND s.status = 'Scheduled'");
$stmt->bind_param('i', $sessionId);
$stmt->execute();
$session = $stmt->get_result()->fetch_assoc();

if (!$session || $session['volunteer_id'] != $_SESSION['user_id']) {
    header('Location: index.php?err=' . urlencode('This session cannot be updated.'));
    exit;
}

$conn->begin_transaction();
try {
    $conn->query("UPDATE sessions SET status = 'Completed' WHERE id = " . (int)$sessionId);
    $conn->query("UPDATE support_requests SET status = 'Completed' WHERE id = " . (int)$session['request_id']);
    $conn->commit();
    header('Location: index.php?msg=' . urlencode('Session marked as completed. The student can now leave feedback.'));
} catch (Exception $e) {
    $conn->rollback();
    header('Location: index.php?err=' . urlencode('Something went wrong. Please try again.'));
}
exit;
