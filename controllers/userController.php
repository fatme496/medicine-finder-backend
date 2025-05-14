<?php
require_once '../models/userModel.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;

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

function handleLogin($conn, $secret_key) {
    // Headers
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type');

    // Read input
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        return;
    }

    $email = $data['email'];
    $password = $data['password'];

    // Model
    $user = findUserByEmail($conn, $email);

    if (!$user) {
        http_response_code(400);
        echo json_encode(['error' => 'This user does not exist']);
        return;
    }

    if (!password_verify($password, $user['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Incorrect password']);
        return;
    }

    // Token
    $payload = [
        'id' => $user['id'],
        'role' => $user['role'],
        'iat' => time(),
        'exp' => time() + 3600
    ];
    $token = JWT::encode($payload, $secret_key, 'HS256');

    // Cookie
    $isLocalhost = $_SERVER['SERVER_NAME'] === 'localhost';
    $isRender = getenv('RENDER') === 'true';
    $isVercel = getenv('VERCEL') === '1';

    $sameSite = ($isRender || $isVercel) ? 'None' : 'Lax';
    $secure = ($isRender || $isVercel);

    setcookie("token", $token, [
        'expires' => time() + 3600,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => $sameSite
    ]);

    echo json_encode(['message' => 'Login successful', 'token' => $token]);
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