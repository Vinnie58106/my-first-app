<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config.php';

// Get the JSON data
$data = json_decode(file_get_contents('php://input'), true);

$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? $data['password'] : '';

// Prepare response
$response = [];

if (empty($email) || empty($password)) {
    $response = ['success' => false, 'message' => 'Please enter email and password'];
    echo json_encode($response);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct - start session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            $response = ['success' => true, 'message' => 'Login successful'];
        } else {
            $response = ['success' => false, 'message' => 'Invalid email or password'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Invalid email or password'];
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
}

// Clear any accidental output before sending JSON
ob_clean();
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>