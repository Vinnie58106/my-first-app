<?php
require_once '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$registration_id = $data['registration_id'] ?? 0;

$pdo->beginTransaction();

// Get course_id
$stmt = $pdo->prepare("SELECT course_id FROM registrations WHERE id = ?");
$stmt->execute([$registration_id]);
$reg = $stmt->fetch();

if ($reg) {
    $stmt = $pdo->prepare("UPDATE registrations SET status = 'dropped' WHERE id = ?");
    $stmt->execute([$registration_id]);
    
    $stmt = $pdo->prepare("UPDATE courses SET enrolled = enrolled - 1 WHERE id = ?");
    $stmt->execute([$reg['course_id']]);
}

$pdo->commit();

echo json_encode(['success' => true, 'message' => 'Student removed from course']);
?>