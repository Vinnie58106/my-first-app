<?php
require_once '../config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$course_id = $data['course_id'] ?? 0;

$stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
$stmt->execute([$course_id]);

echo json_encode(['success' => true, 'message' => 'Course deleted successfully']);
?>