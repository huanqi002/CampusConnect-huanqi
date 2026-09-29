<?php
require 'config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: select_user.php');
    exit;
}

$myId   = $_SESSION['user_id'];
$myRole = $_SESSION['role'];

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
$rows = $stmt->get_result();

include 'header.php';
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
            <form method="post" action="mark_completed.php" style="display:inline">
                <input type="hidden" name="session_id" value="<?php echo (int)$r['session_id']; ?>">
                <button type="submit" class="btn btn-primary">Mark session as completed</button>
            </form>
            <?php endif; ?>
            <form method="post" action="cancel_session.php" onsubmit="return confirm('Cancel this session?');" style="display:inline">
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
                <a class="btn btn-primary" href="feedback.php?session_id=<?php echo (int)$r['session_id']; ?>">Rate this session</a>
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

<?php include 'footer.php'; ?>
