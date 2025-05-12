<?php
require_once '../config/db.php';

function addLocation($conn,$userId, $governorate, $district, $village, $address) {
    $stmt = $conn->prepare("
        INSERT INTO locations (user_id, governorate, district, village, address)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issss", $userId, $governorate, $district, $village, $address);
    if ($stmt->execute()) {
        return ['success' => true, 'location_id' => $stmt->insert_id];
    } else {
        return ['success' => false, 'message' => $conn->error];
    }
}

function getLocationsByUserId($conn,$userId) {
    $stmt = $conn->prepare("SELECT * FROM locations WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}