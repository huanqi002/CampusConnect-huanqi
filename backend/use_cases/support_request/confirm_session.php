<?php
require __DIR__ . '/../../general/config.php';
require __DIR__ . '/../../general/user_profile.php';
require __DIR__ . '/../../general/support_request.php';
require __DIR__ . '/../../general/session.php';

requireLogin();

$requestId   = (int)($_POST['request_id'] ?? 0);
$volunteerId = (int)($_POST['volunteer_id'] ?? 0);
$date        = $_POST['session_date'] ?? '';
$time        = $_POST['session_time'] ?? '';
$mode        = $_POST['mode'] ?? '';

if (!$requestId || !$volunteerId || !$date || !$time || !in_array($mode, ['Online','In-Person'], true)) {
    header('Location: schedule.php?request_id=' . $requestId . '&err=' . urlencode('Please fill in every field.'));
    exit;
}

// Make sure the slot is still free (someone else may have booked it in the meantime)
$slot = findAvailableSlot($conn, $volunteerId, $date, $time);

if (!$slot) {
    header('Location: schedule.php?request_id=' . $requestId . '&err=' . urlencode('That time is no longer available. Please choose another.'));
    exit;
}

$conn->begin_transaction();
try {
    createSession($conn, $requestId, $date, $time, $mode);
    bookSlot($conn, (int)$slot['id']);
    updateSupportRequestStatus($conn, $requestId, 'Scheduled');

    $conn->commit();
    header('Location: index.php?msg=' . urlencode('Session booked successfully.'));
} catch (Exception $e) {
    $conn->rollback();
    header('Location: schedule.php?request_id=' . $requestId . '&err=' . urlencode('Something went wrong. Please try again.'));
}
exit;
