<?php
require_once '../config/db.php';
require_once '../models/stockModel.php';

function getStockByMedicine($conn, $name)
{
    $data = getStockByMedicineName($conn, $name);
    if ($data) {
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No stock found for the given medicine.'
        ]);
    }
}

function createStock($conn,$data) {
    if (
        isset($data['user_id']) &&
        isset($data['medicine_id']) &&
        isset($data['quantity']) &&
        isset($data['price']) &&
        isset($data['expiration_date']) &&
        isset($data['location_id'])
    ) {
        $result = addStock(
            $conn,
            $data['user_id'],
            $data['medicine_id'],
            $data['quantity'],
            $data['price'],
            $data['expiration_date'],
            $data['location_id']
        );

        if ($result['success']) {
            echo json_encode(['status' => 'success', 'stock_id' => $result['stock_id']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => $result['message']]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    }
}

?>
