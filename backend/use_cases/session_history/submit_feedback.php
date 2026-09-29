<?php
require __DIR__ . '/../../general/config.php';
require __DIR__ . '/../../general/session.php';

if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../support_request/index.php?err=' . urlencode('Only the student can leave feedback.'));
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
$session = findSessionForFeedback($conn, $sessionId);

if (!$session || $session['student_id'] != $_SESSION['user_id']) {
    header('Location: ../support_request/index.php?err=' . urlencode('Session not found.'));
    exit;
}
if ($session['status'] !== 'Completed') {
    header('Location: feedback.php?session_id=' . $sessionId . '&err=' . urlencode('You can only rate a session after it is completed.'));
    exit;
}

if (hasFeedback($conn, $sessionId)) {
    header('Location: ../support_request/index.php?err=' . urlencode('Feedback has already been submitted for this session.'));
    exit;
}

if (insertFeedback($conn, $sessionId, $rating, $comments)) {
    header('Location: ../support_request/index.php?msg=' . urlencode('Thank you! Your feedback has been saved.'));
} else {
    header('Location: feedback.php?session_id=' . $sessionId . '&err=' . urlencode('Something went wrong. Please try again.'));
}
exit;
