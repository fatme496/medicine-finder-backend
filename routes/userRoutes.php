<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../controllers/userController.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'add':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = json_decode(file_get_contents("php://input"), true);
                createUser($conn,$data);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            }
            break;

        case 'getAll':
            readAllUsers($conn);
            break;

        case 'getById':
            if (isset($_GET['id'])) {
                readUserById($conn,$_GET['id']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing user id']);
            }
            break;
    }
}
?>