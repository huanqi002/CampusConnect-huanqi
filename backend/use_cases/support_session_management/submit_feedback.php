<?php
require __DIR__ . '/../../general/config.php';

if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: index.php?err=' . urlencode('Only the student can leave feedback.'));
    exit;
}

$sessionId = (int)($_POST['session_id'] ?? 0);
$rating    = (int)($_POST['rating'] ?? 0);
$comments  = trim($_POST['comments'] ?? '');

// Validate rating
if ($rating < 1 || $rating > 5) {
    header('Location: feedback.php?session_id=' . $sessionId . '&err=' . urlencode('Please choose a rating from 1 to 5.'));
    exit;
}

// Re-check the session belongs to this student and is completed (alt. course 9a)
$stmt = $conn->prepare("SELECT s.id, s.status, sr.student_id
                         FROM sessions s
                         JOIN support_requests sr ON s.request_id = sr.id
                         WHERE s.id = ?");
$stmt->bind_param('i', $sessionId);
$stmt->execute();
$session = $stmt->get_result()->fetch_assoc();

if (!$session || $session['student_id'] != $_SESSION['user_id']) {
    header('Location: index.php?err=' . urlencode('Session not found.'));
    exit;
}
if ($session['status'] !== 'Completed') {
    header('Location: feedback.php?session_id=' . $sessionId . '&err=' . urlencode('You can only rate a session after it is completed.'));
    exit;
}

$check = $conn->prepare("SELECT id FROM feedback WHERE session_id = ?");
$check->bind_param('i', $sessionId);
$check->execute();
if ($check->get_result()->fetch_assoc()) {
    header('Location: index.php?err=' . urlencode('Feedback has already been submitted for this session.'));
    exit;
}

$insert = $conn->prepare("INSERT INTO feedback (session_id, rating, comments) VALUES (?, ?, ?)");
$insert->bind_param('iis', $sessionId, $rating, $comments);

if ($insert->execute()) {
    header('Location: index.php?msg=' . urlencode('Thank you! Your feedback has been saved.'));
} else {
    header('Location: feedback.php?session_id=' . $sessionId . '&err=' . urlencode('Something went wrong. Please try again.'));
}
exit;
