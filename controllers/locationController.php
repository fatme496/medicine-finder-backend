<?php
require_once '../models/locationModel.php';

function createLocation($conn,$data) {
    if (
        isset($data['user_id']) &&
        isset($data['governorate']) &&
        isset($data['district']) &&
        isset($data['village']) &&
        isset($data['address'])
    ) {
        $result = addLocation(
            $conn,
            $data['user_id'],
            $data['governorate'],
            $data['district'],
            $data['village'],
            $data['address']
        );
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    }
}

function fetchUserLocations($conn,$userId) {
    $locations = getLocationsByUserId($conn,$userId);
    echo json_encode(['success' => true, 'data' => $locations]);
}