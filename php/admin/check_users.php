<?php
$host = 'localhost';
$dbname = 'greenfield_registration';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all users
    $stmt = $pdo->query("SELECT id, username, full_name, email, role, password FROM users");
    $users = $stmt->fetchAll();
    
    echo "<h1>Users in Database</h1>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Username</th><th>Full Name</th><th>Email</th><th>Role</th><th>Password Hash</th></tr>";
    
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . $user['username'] . "</td>";
        echo "<td>" . $user['full_name'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . $user['role'] . "</td>";
        echo "<td style='font-family:monospace;font-size:11px;'>" . $user['password'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Test Password Verification</h2>";
    
    // Test specific email
    $testEmail = 'riskyrennee@gmail.com';
    echo "<p>Testing email: <strong>$testEmail</strong></p>";
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$testEmail]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p>✅ User found in database!</p>";
        echo "<p>Stored password hash: <code>" . $user['password'] . "</code></p>";
        
        // Test with '123456'
        $testPassword = '123456';
        if (password_verify($testPassword, $user['password'])) {
            echo "<p style='color:green'>✅ Password '$testPassword' MATCHES!</p>";
        } else {
            echo "<p style='color:red'>❌ Password '$testPassword' does NOT match</p>";
        }
        
        // Test with 'password123'
        $testPassword2 = 'password123';
        if (password_verify($testPassword2, $user['password'])) {
            echo "<p style='color:green'>✅ Password '$testPassword2' MATCHES!</p>";
        } else {
            echo "<p style='color:red'>❌ Password '$testPassword2' does NOT match</p>";
        }
        
        // Test with 'vinnie123'
        $testPassword3 = 'vinnie123';
        if (password_verify($testPassword3, $user['password'])) {
            echo "<p style='color:green'>✅ Password '$testPassword3' MATCHES!</p>";
        } else {
            echo "<p style='color:red'>❌ Password '$testPassword3' does NOT match</p>";
        }
    } else {
        echo "<p style='color:red'>❌ User with email $testEmail NOT found in database!</p>";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>