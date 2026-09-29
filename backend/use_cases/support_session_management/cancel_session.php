<?php
require __DIR__ . '/../../general/config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: select_user.php');
    exit;
}

$sessionId = (int)($_POST['session_id'] ?? 0);
$myId      = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT s.*, sr.student_id, sr.volunteer_id
                         FROM sessions s
                         JOIN support_requests sr ON s.request_id = sr.id
                         WHERE s.id = ? AND s.status = 'Scheduled'");
$stmt->bind_param('i', $sessionId);
$stmt->execute();
$session = $stmt->get_result()->fetch_assoc();

if (!$session || ($session['student_id'] != $myId && $session['volunteer_id'] != $myId)) {
    header('Location: index.php?err=' . urlencode('This session cannot be cancelled.'));
    exit;
}

$conn->begin_transaction();
try {
    $conn->query("UPDATE sessions SET status = 'Cancelled' WHERE id = " . (int)$sessionId);
    // A cancelled session goes back to Accepted so a new session can be booked
    $conn->query("UPDATE support_requests SET status = 'Accepted' WHERE id = " . (int)$session['request_id']);
    // Free the volunteer's time slot again
    $updateSlot = $conn->prepare("UPDATE volunteer_availability SET is_booked = 0
                                   WHERE volunteer_id = ? AND available_date = ? AND available_time = ?");
    $updateSlot->bind_param('iss', $session['volunteer_id'], $session['session_date'], $session['session_time']);
    $updateSlot->execute();

    $conn->commit();
    // In a full system this is where the other participant would be notified (e.g. by email)
    header('Location: index.php?msg=' . urlencode('Session cancelled. The other participant has been notified.'));
} catch (Exception $e) {
    $conn->rollback();
    header('Location: index.php?err=' . urlencode('Something went wrong. Please try again.'));
}
exit;
