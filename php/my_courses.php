<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT c.*, r.registration_date 
    FROM courses c
    JOIN registrations r ON c.id = r.course_id
    WHERE r.user_id = ? AND r.status = 'active'
    ORDER BY r.registration_date DESC
");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();

echo json_encode($courses);
?>