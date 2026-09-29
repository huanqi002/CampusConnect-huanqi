<?php
require 'config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: select_user.php');
    exit;
}

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
$check = $conn->prepare("SELECT id FROM volunteer_availability
                          WHERE volunteer_id = ? AND available_date = ? AND available_time = ? AND is_booked = 0");
$check->bind_param('iss', $volunteerId, $date, $time);
$check->execute();
$slot = $check->get_result()->fetch_assoc();

if (!$slot) {
    header('Location: schedule.php?request_id=' . $requestId . '&err=' . urlencode('That time is no longer available. Please choose another.'));
    exit;
}

$conn->begin_transaction();
try {
    $insert = $conn->prepare("INSERT INTO sessions (request_id, session_date, session_time, mode, status)
                               VALUES (?, ?, ?, ?, 'Scheduled')");
    $insert->bind_param('isss', $requestId, $date, $time, $mode);
    $insert->execute();

    $updateSlot = $conn->prepare("UPDATE volunteer_availability SET is_booked = 1 WHERE id = ?");
    $updateSlot->bind_param('i', $slot['id']);
    $updateSlot->execute();

    $updateRequest = $conn->prepare("UPDATE support_requests SET status = 'Scheduled' WHERE id = ?");
    $updateRequest->bind_param('i', $requestId);
    $updateRequest->execute();

    $conn->commit();
    header('Location: index.php?msg=' . urlencode('Session booked successfully.'));
} catch (Exception $e) {
    $conn->rollback();
    header('Location: schedule.php?request_id=' . $requestId . '&err=' . urlencode('Something went wrong. Please try again.'));
}
exit;
