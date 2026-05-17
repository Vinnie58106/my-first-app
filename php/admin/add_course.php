<?php
require_once '../config.php';

// Set header to return JSON
header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Admin access required.']);
    exit();
}

// Get the JSON data sent from the form
$data = json_decode(file_get_contents('php://input'), true);

// Get form values
$course_code = isset($data['course_code']) ? trim($data['course_code']) : '';
$course_name = isset($data['course_name']) ? trim($data['course_name']) : '';
$description = isset($data['description']) ? trim($data['description']) : '';
$credits = isset($data['credits']) ? intval($data['credits']) : 3;
$instructor = isset($data['instructor']) ? trim($data['instructor']) : '';
$capacity = isset($data['capacity']) ? intval($data['capacity']) : 30;
$room = isset($data['room']) ? trim($data['room']) : '';
$schedule = isset($data['schedule']) ? trim($data['schedule']) : '';

// Validate required fields
if (empty($course_code) || empty($course_name) || empty($instructor) || empty($schedule)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields (Course Code, Course Name, Instructor, Schedule)']);
    exit();
}

try {
    // Check if course code already exists
    $stmt = $pdo->prepare("SELECT id FROM courses WHERE course_code = ?");
    $stmt->execute([$course_code]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Course code already exists. Please use a unique code.']);
        exit();
    }
    
    // Insert the new course
    $stmt = $pdo->prepare("INSERT INTO courses (course_code, course_name, description, credits, instructor, capacity, room, schedule) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([$course_code, $course_name, $description, $credits, $instructor, $capacity, $room, $schedule]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Course added successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add course. Please try again.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>