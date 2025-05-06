<?php
require_once '../config/db.php';
require_once '../models/medicineModel.php';

function getAllMedicinesController($conn)
{
    header('Content-Type: application/json');

    try {
        $result = getAllMedicines($conn);

        if ($result && $result->num_rows > 0) {
            $medicines = [];

            while ($row = $result->fetch_assoc()) {
                $medicines[] = $row;
            }

            http_response_code(200);
            echo json_encode([
                "data" => $medicines,
                "message" => "Medicines retrieved successfully.",
                "error" => null
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "data" => [],
                "message" => "No medicines found.",
                "error" => null
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "data" => null,
            "message" => "Error retrieving medicines.",
            "error" => $e->getMessage()
        ]);
    }
}


// GET MEDICINE BY ID
function getMedicineByIdController($conn, $id)
{
    header('Content-Type: application/json');

    try {
        $result = getMedicineById($conn, $id);

        if ($result && $result->num_rows > 0) {
            $medicine = $result->fetch_assoc();

            http_response_code(200);
            echo json_encode([
                "data" => $medicine,
                "message" => "Medicine found.",
                "error" => null
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "data" => null,
                "message" => "Medicine not found.",
                "error" => null
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "data" => null,
            "message" => "Error retrieving medicine.",
            "error" => $e->getMessage()
        ]);
    }
}


function createMedicineController($conn)
{
    header('Content-Type: application/json');

    // Read raw JSON input
    $input = json_decode(file_get_contents("php://input"), true);

    // Extract fields from JSON body
    $name = $input['name'] ?? null;
    $category = $input['category'] ?? null;
    $dosage = $input['dosage'] ?? null;
    $description = $input['description'] ?? null;

    if (!$name || !$category || !$dosage || !$description) {
        http_response_code(400);
        echo json_encode([
            "data" => null,
            "message" => "All fields are required.",
            "error" => "Missing parameters."
        ]);
        return;
    }

    try {
        $result = createMedicine($conn, $name, $category, $dosage, $description);

        if ($result) {

            // Get the inserted ID
            $insertedId = $conn->insert_id;

            $insertedData = [
                "id" => $insertedId,
                "name" => $name,
                "category" => $category,
                "dosage" => $dosage,
                "description" => $description
            ];
            http_response_code(201);
            echo json_encode([
                "data" => $insertedData,
                "message" => "Medicine created successfully."
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "data" => null,
                "message" => "Failed to create medicine.",
                "error" => "Database insert error."
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "data" => null,
            "message" => "Error creating medicine.",
            "error" => $e->getMessage()
        ]);
    }
}

// UPDATE MEDICINE
function updateMedicineController($conn, $id)
{
    header('Content-Type: application/json');

    // Read raw JSON input
    $input = json_decode(file_get_contents("php://input"), true);

    $name = $input['name'] ?? null;
    $category = $input['category'] ?? null;
    $dosage = $input['dosage'] ?? null;
    $description = $input['description'] ?? null;

    if (!$name || !$category || !$dosage || !$description) {
        http_response_code(400);
        echo json_encode([
            "data" => null,
            "message" => "All fields are required.",
            "error" => "Missing parameters."
        ]);
        return;
    }

    try {
        $stmt = updateMedicine($conn, $id, $name, $category, $dosage, $description);

        if ($stmt && $conn->affected_rows > 0) {
            http_response_code(200);
            echo json_encode([
                "data" => [
                    "id" => $id,
                    "name" => $name,
                    "category" => $category,
                    "dosage" => $dosage,
                    "description" => $description
                ],
                "message" => "Medicine updated successfully.",
                "error" => null
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "data" => null,
                "message" => "Medicine not found or no changes made.",
                "error" => null
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "data" => null,
            "message" => "Error updating medicine.",
            "error" => $e->getMessage()
        ]);
    }
}


// DELETE MEDICINE
function deleteMedicineController($conn, $id)
{
    header('Content-Type: application/json');

    try {
        $result = deleteMedicine($conn, $id);

        if ($result && $conn->affected_rows > 0) {
            http_response_code(200);
            echo json_encode([
                "data" => ["id" => $id],
                "message" => "Medicine deleted successfully.",
                "error" => null
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "data" => null,
                "message" => "Medicine not found.",
                "error" => null
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "data" => null,
            "message" => "Error deleting medicine.",
            "error" => $e->getMessage()
        ]);
    }
}

function searchMedicinesController($conn)
{
    header('Content-Type: application/json');

    $name = $_GET['name'] ?? '';

    if (!$name) {
        http_response_code(400);
        echo json_encode([
            "data" => null,
            "message" => "Name parameter is required.",
            "error" => "Missing name query parameter."
        ]);
        return;
    }

    try {
        $medicines = searchMedicinesByName($conn, $name);

        if (!empty($medicines)) {
            http_response_code(200);
            echo json_encode([
                "data" => $medicines,
                "message" => "Medicines found.",
                "error" => null
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "data" => null,
                "message" => "No medicines found for the given name.",
                "error" => null
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "data" => null,
            "message" => "Error searching medicines.",
            "error" => $e->getMessage()
        ]);
    }
}


function searchMedicineByNameAndLocationController($conn)
{
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents("php://input"), true);

    $name = $input['name'] ?? null;
    $governorate = $input['governorate'] ?? null;
    $district = $input['district'] ?? null;

    if (!$name || !$governorate || !$district) {
        http_response_code(400);
        echo json_encode([
            "data" => null,
            "message" => "All fields (name, governorate, district) are required.",
            "error" => "Missing parameters"
        ]);
        return;
    }

    try {
        $results = searchMedicineByNameAndLocation($conn, $name, $governorate, $district);

        if ($results && count($results) > 0) {
            http_response_code(200);
            echo json_encode([
                "data" => $results,
                "message" => count($results) . " result(s) found.",
                "error" => null
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "data" => [],
                "message" => "No medicines found for the given criteria.",
                "error" => null
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "data" => null,
            "message" => "Server error occurred.",
            "error" => $e->getMessage()
        ]);
    }
}
