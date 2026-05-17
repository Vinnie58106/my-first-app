<?php
require_once 'config.php';

$search = $_GET['search'] ?? '';

$sql = "SELECT *, (capacity - enrolled) as spots_left, (enrolled >= capacity) as is_full 
        FROM courses WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (course_name LIKE ? OR course_code LIKE ? OR instructor LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY course_code";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll();

// Check which courses user is registered for
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $regStmt = $pdo->prepare("SELECT course_id FROM registrations WHERE user_id = ? AND status = 'active'");
    $regStmt->execute([$userId]);
    $registered = $regStmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($courses as &$course) {
        $course['is_registered'] = in_array($course['id'], $registered);
    }
}

echo json_encode($courses);
?>