<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['full_name'] = 'Administrator';

require_once 'config.php';

echo "<h1>Test Add Course API</h1>";

// Test data
$testCourse = [
    'course_code' => 'TEST101',
    'course_name' => 'Test Course',
    'description' => 'This is a test course',
    'credits' => 3,
    'instructor' => 'Dr. Test',
    'capacity' => 30,
    'room' => 'Room 101',
    'schedule' => 'Mon/Wed 1:00-2:30 PM'
];

echo "<h2>Sending test course:</h2>";
echo "<pre>";
print_r($testCourse);
echo "</pre>";

// Send to add_course.php
$ch = curl_init('http://localhost/greenfield/php/admin/add_course.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testCourse));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<h2>Response:</h2>";
echo "<p>HTTP Code: $httpCode</p>";
echo "<pre>$response</pre>";

// Check if course was added
$stmt = $pdo->query("SELECT * FROM courses WHERE course_code = 'TEST101'");
$course = $stmt->fetch();

if ($course) {
    echo "<p style='color:green'>✅ Course was added successfully!</p>";
} else {
    echo "<p style='color:red'>❌ Course was NOT added</p>";
}

// Show all courses
echo "<h2>All Courses in Database:</h2>";
$allCourses = $pdo->query("SELECT * FROM courses")->fetchAll();
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>ID</th><th>Code</th><th>Name</th><th>Instructor</th></tr>";
foreach ($allCourses as $c) {
    echo "<tr>";
    echo "<td>{$c['id']}</td>";
    echo "<td>{$c['course_code']}</td>";
    echo "<td>{$c['course_name']}</td>";
    echo "<td>{$c['instructor']}</td>";
    echo "</tr>";
}
echo "</table>";
?>