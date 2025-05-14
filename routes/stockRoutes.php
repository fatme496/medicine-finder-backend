<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../controllers/stockController.php';

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        case 'getStockByMedicine':
            if (isset($_GET['name'])) {
                getStockByMedicine($conn, $_GET['name']);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Missing medicine name'
                ]);
            }
            break;

        case 'searchGrouped':
            if (isset($_GET['name'])) {
                $name = $_GET['name'];
                $governorate = $_GET['governorate'] ?? '';
                $district = $_GET['district'] ?? '';

                searchGroupedByMedicine($conn, $name, $governorate, $district);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Missing medicine name'
                ]);
            }
            break;

        case 'addStock':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = json_decode(file_get_contents("php://input"), true);
                createStock($conn, $data);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
            }
            break;


        default:
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid action'
            ]);
            break;
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Action not specified'
    ]);
}
