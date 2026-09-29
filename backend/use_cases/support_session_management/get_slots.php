<?php
require __DIR__ . '/../../general/config.php';
header('Content-Type: application/json');

$volunteerId = isset($_GET['volunteer_id']) ? (int)$_GET['volunteer_id'] : 0;
$date        = $_GET['date'] ?? '';

if (!$volunteerId || !$date) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT available_time FROM volunteer_availability
                         WHERE volunteer_id = ? AND available_date = ? AND is_booked = 0
                         ORDER BY available_time");
$stmt->bind_param('is', $volunteerId, $date);
$stmt->execute();
$result = $stmt->get_result();

$times = [];
while ($row = $result->fetch_assoc()) {
    $times[] = substr($row['available_time'], 0, 5); // HH:MM
}

echo json_encode($times);
