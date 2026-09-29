<?php
require __DIR__ . '/../../general/config.php';
require __DIR__ . '/../../general/support_request.php';
require __DIR__ . '/../../general/session.php';

if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'volunteer') {
    header('Location: ../support_request/index.php?err=' . urlencode('Only the volunteer can mark a session as completed.'));
    exit;
}

$sessionId = (int)($_POST['session_id'] ?? 0);
$session   = findScheduledSession($conn, $sessionId);

if (!$session || $session['volunteer_id'] != $_SESSION['user_id']) {
    header('Location: ../support_request/index.php?err=' . urlencode('This session cannot be updated.'));
    exit;
}

$conn->begin_transaction();
try {
    updateSessionStatus($conn, $sessionId, 'Completed');
    updateSupportRequestStatus($conn, (int)$session['request_id'], 'Completed');
    $conn->commit();
    header('Location: ../support_request/index.php?msg=' . urlencode('Session marked as completed. The student can now leave feedback.'));
} catch (Exception $e) {
    $conn->rollback();
    header('Location: ../support_request/index.php?err=' . urlencode('Something went wrong. Please try again.'));
}
exit;
