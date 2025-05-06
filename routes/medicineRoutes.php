<?php
require_once __DIR__ . '/../controllers/medicineController.php';
require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        switch ($action) {
            case 'getAll':
                getAllMedicinesController($conn);
                break;

            case 'get':
                if ($id) {
                    getMedicineByIdController($conn, $id);
                } else {
                    http_response_code(400);
                    echo json_encode(['data' => null, 'message' => 'Missing ID parameter', 'error' => '']);
                }
                break;

            case 'searchbyname':
                if (isset($_GET['name'])) {
                    searchMedicinesController($conn);
                } else {
                    http_response_code(400);
                    echo json_encode(['data' => null, 'message' => 'Missing name parameter', 'error' => '']);
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(['data' => null, 'message' => 'Route not found', 'error' => 'Invalid action']);
        }
        break;

    case 'POST':
        switch ($action) {
            case 'create':
                createMedicineController($conn);
                break;

            case 'search':
                searchMedicineByNameAndLocationController($conn);
                break;

            default:
                http_response_code(404);
                echo json_encode(['data' => null, 'message' => 'Route not found', 'error' => 'Invalid action']);
        }
        break;

    case 'PUT':
        if ($action === 'update' && $id) {
            updateMedicineController($conn, $id);
        } else {
            http_response_code(400);
            echo json_encode(['data' => null, 'message' => 'Missing ID or invalid action', 'error' => '']);
        }
        break;

    case 'DELETE':
        if ($action === 'delete' && $id) {
            deleteMedicineController($conn, $id);
        } else {
            http_response_code(400);
            echo json_encode(['data' => null, 'message' => 'Missing ID or invalid action', 'error' => '']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['data' => null, 'message' => 'Method Not Allowed', 'error' => '']);
}
?>