<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config.php';

header('Content-Type: application/json');

// For testing - allow access if we're in test mode or if admin session exists
$isTestMode = isset($_GET['test']) && $_GET['test'] == 1;

if (!$isTestMode) {
    // Normal mode - check session
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Not logged in. Please login first.']);
        exit();
    }
    
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized. Admin access required.']);
        exit();
    }
}

try {
    $stmt = $pdo->query("
        SELECT r.id as registration_id, 
               u.full_name as student_name, 
               u.email,
               c.course_name,
               c.course_code,
               r.registration_date,
               r.status
        FROM registrations r
        JOIN users u ON r.user_id = u.id
        JOIN courses c ON r.course_id = c.id
        WHERE r.status = 'active'
        ORDER BY r.registration_date DESC
    ");

    $registrations = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $registrations]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>