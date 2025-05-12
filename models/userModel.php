<?php

function addUser($conn,$name, $email, $password, $role, $phone)
{

    $stmt = $conn->prepare("
        INSERT INTO users (name, email, password, role, phone)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssss", $name, $email, $password, $role, $phone);

    if ($stmt->execute()) {
        return ['success' => true, 'user_id' => $stmt->insert_id];
    } else {
        return ['success' => false, 'message' => $conn->error];
    }
}

function getAllUsers($conn)
{

    $result = $conn->query("SELECT id, name, email, role, phone FROM users");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getUserById($conn,$id)
{
    $stmt = $conn->prepare("SELECT id, name, email, role, phone FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
