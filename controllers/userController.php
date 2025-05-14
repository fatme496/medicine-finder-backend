<?php
require_once '../models/userModel.php';

function createUser($conn,$data) {
    if (
        isset($data['name']) &&
        isset($data['email']) &&
        isset($data['password']) &&
        isset($data['role']) &&
        isset($data['phone'])
    ) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $result = addUser(
            $conn,
            $data['name'],
            $data['email'],
            $hashedPassword,
            $data['role'],
            $data['phone']
        );
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
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