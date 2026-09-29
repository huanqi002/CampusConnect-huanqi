<?php
require __DIR__ . '/../../general/config.php';
require __DIR__ . '/../../general/user_profile.php';
require __DIR__ . '/../../general/support_request.php';
require __DIR__ . '/../../general/session.php';

requireLogin();

$sessionId = (int)($_POST['session_id'] ?? 0);
$myId      = currentUser()['id'];

$session = findScheduledSession($conn, $sessionId);

if (!$session || ($session['student_id'] != $myId && $session['volunteer_id'] != $myId)) {
    header('Location: ../support_request/index.php?err=' . urlencode('This session cannot be cancelled.'));
    exit;
}

$conn->begin_transaction();
try {
    updateSessionStatus($conn, $sessionId, 'Cancelled');
    // A cancelled session goes back to Accepted so a new session can be booked
    updateSupportRequestStatus($conn, (int)$session['request_id'], 'Accepted');
    // Free the volunteer's time slot again
    freeSlot($conn, (int)$session['volunteer_id'], $session['session_date'], $session['session_time']);

    $conn->commit();
    // In a full system this is where the other participant would be notified (e.g. by email)
    header('Location: ../support_request/index.php?msg=' . urlencode('Session cancelled. The other participant has been notified.'));
} catch (Exception $e) {
    $conn->rollback();
    header('Location: ../support_request/index.php?err=' . urlencode('Something went wrong. Please try again.'));
}
exit;
