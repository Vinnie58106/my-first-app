<?php
// Start a REAL admin session
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['full_name'] = 'Administrator';
$_SESSION['username'] = 'admin';
$_SESSION['email'] = 'admin@greenfield.edu';

require_once 'config.php';

echo "<h1>Test Registrations API</h1>";

// Now call the API with the active session
$ch = curl_init('http://localhost/greenfield/php/admin/get_all_registrations.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_COOKIEFILE, ''); // Use session cookies

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: $httpCode</p>";
echo "<h2>Raw Response:</h2>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Parse JSON
$data = json_decode($response, true);
if ($data) {
    echo "<h2>Parsed Response:</h2>";
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    
    if ($data['success'] && !empty($data['data'])) {
        echo "<h2 style='color:green'>✅ Registrations Found!</h2>";
        echo "<table border='1' cellpadding='8'>";
        echo "<tr><th>Student</th><th>Course</th><th>Date</th></tr>";
        foreach ($data['data'] as $reg) {
            echo "<tr>";
            echo "<td>{$reg['student_name']}</td>";
            echo "<td>{$reg['course_name']} ({$reg['course_code']})</td>";
            echo "<td>{$reg['registration_date']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red'>❌ No registrations found or API returned error</p>";
        echo "<p>Message: " . ($data['message'] ?? 'No message') . "</p>";
    }
} else {
    echo "<p style='color:red'>❌ Failed to parse JSON response</p>";
}

// Show registrations directly from database
echo "<h2>Direct Database Check:</h2>";
$stmt = $pdo->query("SELECT COUNT(*) FROM registrations WHERE status = 'active'");
$count = $stmt->fetchColumn();
echo "<p>Total active registrations in database: <strong>$count</strong></p>";

if ($count > 0) {
    $stmt = $pdo->query("
        SELECT r.*, u.full_name as student_name, c.course_name 
        FROM registrations r
        JOIN users u ON r.user_id = u.id
        JOIN courses c ON r.course_id = c.id
        WHERE r.status = 'active'
        LIMIT 5
    ");
    $regs = $stmt->fetchAll();
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>ID</th><th>Student</th><th>Course</th><th>Status</th></tr>";
    foreach ($regs as $reg) {
        echo "<tr>";
        echo "<td>{$reg['id']}</td>";
        echo "<td>{$reg['student_name']}</td>";
        echo "<td>{$reg['course_name']}</td>";
        echo "<td>{$reg['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Check admin session
echo "<h2>Session Check:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>