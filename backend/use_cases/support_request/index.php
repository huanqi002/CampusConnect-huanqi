<?php
require __DIR__ . '/../../general/config.php';
require __DIR__ . '/../../general/user_profile.php';
require __DIR__ . '/../../general/history.php';

requireLogin();

$me     = currentUser();
$myId   = $me['id'];
$myRole = $me['role'];

$rows = fetchMyActivity($conn, $myId);

include __DIR__ . '/../../general/header.php';
?>

<h1>My sessions</h1>
<p class="subtitle">Here are your support requests and sessions. Choose an action below to continue.</p>

<?php if (isset($_GET['msg'])): ?>
<div class="message message-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>
<?php if (isset($_GET['err'])): ?>
<div class="message message-error"><?php echo htmlspecialchars($_GET['err']); ?></div>
<?php endif; ?>

<?php if ($rows->num_rows === 0): ?>
<p>You have no support requests yet.</p>
<?php endif; ?>

<?php while ($r = $rows->fetch_assoc()):
    $otherPersonName = ($myRole === 'student') ? $r['volunteer_name'] : $r['student_name'];
    $otherPersonLabel = ($myRole === 'student') ? 'Volunteer' : 'Student';
?>
<div class="card">
    <span class="status status-<?php echo htmlspecialchars($r['request_status']); ?>"><?php echo htmlspecialchars($r['request_status']); ?></span>
    <h3><?php echo htmlspecialchars($r['subject']); ?></h3>
    <p class="meta"><?php echo $otherPersonLabel; ?>: <?php echo htmlspecialchars($otherPersonName); ?></p>

    <?php if ($r['request_status'] === 'Accepted'): ?>
        <p class="meta">No session booked yet.</p>
        <div class="btn-row">
            <a class="btn btn-primary" href="schedule.php?request_id=<?php echo (int)$r['request_id']; ?>">Book a session</a>
        </div>

    <?php elseif ($r['request_status'] === 'Scheduled'): ?>
        <p class="meta">Date: <strong><?php echo htmlspecialchars($r['session_date']); ?></strong>
           at <strong><?php echo substr($r['session_time'],0,5); ?></strong>
           &mdash; <?php echo htmlspecialchars($r['mode']); ?></p>
        <div class="btn-row">
            <?php if ($myRole === 'volunteer'): ?>
            <form method="post" action="../session_history/mark_completed.php" style="display:inline">
                <input type="hidden" name="session_id" value="<?php echo (int)$r['session_id']; ?>">
                <button type="submit" class="btn btn-primary">Mark session as completed</button>
            </form>
            <?php endif; ?>
            <form method="post" action="../session_history/cancel_session.php" onsubmit="return confirm('Cancel this session?');" style="display:inline">
                <input type="hidden" name="session_id" value="<?php echo (int)$r['session_id']; ?>">
                <button type="submit" class="btn btn-secondary">Cancel session</button>
            </form>
        </div>

    <?php elseif ($r['request_status'] === 'Completed'): ?>
        <p class="meta">Session held on <strong><?php echo htmlspecialchars($r['session_date']); ?></strong>
           at <strong><?php echo substr($r['session_time'],0,5); ?></strong>
           &mdash; <?php echo htmlspecialchars($r['mode']); ?></p>
        <?php if ($r['feedback_id']): ?>
            <p class="meta">Feedback given: <strong><?php echo (int)$r['rating']; ?> / 5</strong></p>
        <?php elseif ($myRole === 'student'): ?>
            <div class="btn-row">
                <a class="btn btn-primary" href="../session_history/feedback.php?session_id=<?php echo (int)$r['session_id']; ?>">Rate this session</a>
            </div>
        <?php else: ?>
            <p class="meta">Waiting for the student to leave feedback.</p>
        <?php endif; ?>

    <?php elseif ($r['request_status'] === 'Cancelled'): ?>
        <p class="meta">This session was cancelled.</p>
        <div class="btn-row">
            <a class="btn btn-secondary" href="schedule.php?request_id=<?php echo (int)$r['request_id']; ?>">Book a new session</a>
        </div>
    <?php endif; ?>
</div>
<?php endwhile; ?>

<?php include __DIR__ . '/../../general/footer.php'; ?>
