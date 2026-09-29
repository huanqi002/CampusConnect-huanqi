<?php
require __DIR__ . '/../../general/config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: select_user.php');
    exit;
}

$myId = $_SESSION['user_id'];

$sql = "SELECT sr.subject, sr.status AS request_status,
               us.name AS student_name, uv.name AS volunteer_name,
               s.session_date, s.session_time, s.mode, s.status AS session_status,
               f.rating, f.comments
        FROM support_requests sr
        JOIN users us ON sr.student_id = us.id
        JOIN users uv ON sr.volunteer_id = uv.id
        LEFT JOIN sessions s ON s.request_id = sr.id
        LEFT JOIN feedback f ON f.session_id = s.id
        WHERE sr.student_id = ? OR sr.volunteer_id = ?
        ORDER BY s.session_date DESC, s.session_time DESC, sr.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $myId, $myId);
$stmt->execute();
$rows = $stmt->get_result();

include __DIR__ . '/../../general/header.php';
?>

<h1>Support history</h1>
<p class="subtitle">All of your past and current support requests and sessions.</p>

<?php if ($rows->num_rows === 0): ?>
<p>Nothing to show yet.</p>
<?php endif; ?>

<?php while ($r = $rows->fetch_assoc()): ?>
<div class="card">
    <span class="status status-<?php echo htmlspecialchars($r['request_status']); ?>"><?php echo htmlspecialchars($r['request_status']); ?></span>
    <h3><?php echo htmlspecialchars($r['subject']); ?></h3>
    <p class="meta">Student: <?php echo htmlspecialchars($r['student_name']); ?> &nbsp;|&nbsp; Volunteer: <?php echo htmlspecialchars($r['volunteer_name']); ?></p>

    <?php if ($r['session_date']): ?>
        <p class="meta">Session: <?php echo htmlspecialchars($r['session_date']); ?> at <?php echo substr($r['session_time'],0,5); ?>
           (<?php echo htmlspecialchars($r['mode']); ?>) &mdash; <?php echo htmlspecialchars($r['session_status']); ?></p>
    <?php endif; ?>

    <?php if ($r['rating']): ?>
        <p class="meta">Feedback: <strong><?php echo (int)$r['rating']; ?> / 5</strong>
           <?php if (!empty($r['comments'])): ?> &mdash; "<?php echo htmlspecialchars($r['comments']); ?>"<?php endif; ?>
        </p>
    <?php endif; ?>
</div>
<?php endwhile; ?>

<?php include __DIR__ . '/../../general/footer.php'; ?>
