<?php
require __DIR__ . '/../../general/config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: select_user.php');
    exit;
}

$requestId = isset($_GET['request_id']) ? (int)$_GET['request_id'] : 0;

$stmt = $conn->prepare("SELECT sr.*, us.name AS student_name, uv.name AS volunteer_name
                         FROM support_requests sr
                         JOIN users us ON sr.student_id = us.id
                         JOIN users uv ON sr.volunteer_id = uv.id
                         WHERE sr.id = ?");
$stmt->bind_param('i', $requestId);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();

if (!$request || $request['status'] !== 'Accepted') {
    header('Location: index.php?err=' . urlencode('This request is not ready to be scheduled.'));
    exit;
}

include __DIR__ . '/../../general/header.php';
?>

<h1>Book a session</h1>
<p class="subtitle"><?php echo htmlspecialchars($request['subject']); ?> &mdash;
   Volunteer: <?php echo htmlspecialchars($request['volunteer_name']); ?></p>

<form id="scheduleForm" method="post" action="confirm_session.php">
    <input type="hidden" name="request_id" value="<?php echo (int)$request['id']; ?>">
    <input type="hidden" name="volunteer_id" value="<?php echo (int)$request['volunteer_id']; ?>">

    <label for="session_date">Choose a date</label>
    <input type="date" id="session_date" name="session_date" min="<?php echo date('Y-m-d'); ?>" required>
    <p class="help-text">Available times for this volunteer will appear once you pick a date.</p>

    <label for="session_time">Choose a time</label>
    <select id="session_time" name="session_time" required>
        <option value="">Pick a date first</option>
    </select>
    <p class="help-text" id="slotHelp"></p>

    <label>Choose how the session will happen</label>
    <div class="radio-group">
        <label><input type="radio" name="mode" value="Online" checked> Online</label>
        <label><input type="radio" name="mode" value="In-Person"> In-Person</label>
    </div>

    <div class="btn-row">
        <button type="submit" class="btn btn-primary">Confirm session</button>
        <a class="btn btn-plain" href="index.php">Cancel</a>
    </div>
</form>

<script src="../../../frontend/js/script.js"></script>
<script>
    initScheduleForm(<?php echo (int)$request['volunteer_id']; ?>);
</script>

<?php include __DIR__ . '/../../general/footer.php'; ?>
