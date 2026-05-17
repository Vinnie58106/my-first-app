<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$course_id = $data['course_id'] ?? 0;
$user_id = $_SESSION['user_id'];

$pdo->beginTransaction();

$stmt = $pdo->prepare("UPDATE registrations SET status = 'dropped' WHERE user_id = ? AND course_id = ? AND status = 'active'");
$stmt->execute([$user_id, $course_id]);

$stmt = $pdo->prepare("UPDATE courses SET enrolled = enrolled - 1 WHERE id = ?");
$stmt->execute([$course_id]);
$pdo->commit();

echo json_encode(['success' => true, 'message' => 'Course dropped successfully']);
?>