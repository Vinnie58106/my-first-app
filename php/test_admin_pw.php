<?php
require_once 'config.php';

$email = 'admin@greenfield.edu';
$testPassword = 'password123';

echo "<h1>Testing Admin Login</h1>";

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$admin = $stmt->fetch();

if ($admin) {
    echo "<p>✅ Admin found in database</p>";
    echo "<p>Email: " . $admin['email'] . "</p>";
    echo "<p>Role: " . $admin['role'] . "</p>";
    echo "<p>Testing password: <strong>'$testPassword'</strong></p>";
    
    if (password_verify($testPassword, $admin['password'])) {
        echo "<p style='color:green;font-size:18px;'>✅ PASSWORD IS CORRECT! You can login!</p>";
    } else {
        echo "<p style='color:red;font-size:18px;'>❌ PASSWORD IS WRONG!</p>";
        echo "<p>Let's reset it...</p>";
        
        // Reset password
        $newHash = password_hash('password123', PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->execute([$newHash, $email]);
        
        echo "<p style='color:green'>✅ Password has been RESET to 'password123'</p>";
        echo "<p>Try logging in now!</p>";
    }
} else {
    echo "<p>❌ Admin not found!</p>";
}
?>