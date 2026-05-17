<?php
require_once 'config.php';

echo "<h1>Users in Database</h1>";
echo "<table border='1' cellpadding='10' style='border-collapse:collapse;'>";
echo "<tr style='background:#f0f0f0;'><th>ID</th><th>Username</th><th>Full Name</th><th>Email</th><th>Role</th><th>Try Password</th></tr>";

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();

$testPasswords = ['admin123', 'password123', '123456', 'test123', 'vinnie123', '12345'];

foreach ($users as $user) {
    echo "<tr>";
    echo "<td>" . $user['id'] . "</td>";
    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
    echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
    echo "<td>" . $user['role'] . "</td>";
    echo "<td>";
    
    // Test common passwords
    foreach ($testPasswords as $testPw) {
        if (password_verify($testPw, $user['password'])) {
            echo "<span style='color:green;font-weight:bold;'>✅ $testPw</span><br>";
        }
    }
    
    echo "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Test Specific Email</h2>";
echo "<form method='post'>";
echo "Email: <input type='email' name='test_email' value='riskyrennee@gmail.com' style='width:300px;padding:10px;'>";
echo "<button type='submit'>Test Password</button>";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
    $testEmail = $_POST['test_email'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$testEmail]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<h3>Testing passwords for: " . htmlspecialchars($testEmail) . "</h3>";
        echo "<ul>";
        foreach ($testPasswords as $testPw) {
            if (password_verify($testPw, $user['password'])) {
                echo "<li style='color:green'>✅ Password: <strong>$testPw</strong> WORKS!</li>";
            } else {
                echo "<li style='color:red'>❌ Password: $testPw does NOT work</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p style='color:red'>User not found with email: $testEmail</p>";
    }
}
?>