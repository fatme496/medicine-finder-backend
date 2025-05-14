<?php
require_once '../models/userModel.php';

function createUser($conn, $data) {
    // Extract fields
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $phone = trim($data['phone'] ?? '');

    // 1. Validate required fields
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Name, email, and password are required']);
        return;
    }

    // 2. Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }

    // 3. Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already in use']);
        return;
    }

    // 4. Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 5. Set default role
    $role = 'user';

    // 6. Insert user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $hashedPassword, $role, $phone);
    $result = $stmt->execute();

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Signup successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
}


function readAllUsers($conn) {
    $users = getAllUsers($conn);
    echo json_encode(['success' => true, 'data' => $users]);
}

function readUserById($conn,$id) {
    $user = getUserById($conn,$id);
    if ($user) {
        echo json_encode(['success' => true, 'data' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
}
?>