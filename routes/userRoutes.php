<?php
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../controllers/userController.php';


$secret_key = "your_secret_key_here";

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'register':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = json_decode(file_get_contents("php://input"), true);
                createUser($conn, $data);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            }
            break;

        case 'getAll':
            readAllUsers($conn);
            break;

        case 'getById':
            if (isset($_GET['id'])) {
                readUserById($conn, $_GET['id']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing user id']);
            }
            break;

        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                handleLogin($conn, $secret_key);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            }
            break;
    }
}
