<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'logged_in' => true,
        'user_id' => $_SESSION['user_id'],
        'full_name' => $_SESSION['full_name'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'role' => $_SESSION['role']
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>