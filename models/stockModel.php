<?php

function getStockByMedicineName($conn, $name)
{

    $sql = "
        SELECT 
            m.id AS medicine_id,
            m.name AS medicine_name,
            m.category,
            m.dosage,
            m.description,
            s.id AS stock_id,
            s.quantity,
            s.price,
            s.expiration_date,
            l.governorate,
            l.district,
            l.village,
            l.address,
            u.name AS provider_name
        FROM medicines m
        JOIN stock s ON m.id = s.medicine_id
        JOIN stock_locations sl ON s.id = sl.stock_id
        JOIN locations l ON sl.location_id = l.id
        JOIN users u ON s.user_id = u.id
        WHERE m.name LIKE ?
    ";

    $stmt = $conn->prepare($sql);
    $search = "%$name%";
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $stock = [];
    while ($row = $result->fetch_assoc()) {
        $stock[] = $row;
    }

    return $stock;
}

function addStock($conn,$userId, $medicineId, $quantity, $price, $expirationDate, $locationId)
{
    // Insert into stock table
    $stmt = $conn->prepare("
        INSERT INTO stock (user_id, medicine_id, quantity, price, expiration_date)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiids", $userId, $medicineId, $quantity, $price, $expirationDate);

    if ($stmt->execute()) {
        $stockId = $stmt->insert_id;

        // Link with location
        $linkStmt = $conn->prepare("INSERT INTO stock_locations (stock_id, location_id) VALUES (?, ?)");
        $linkStmt->bind_param("ii", $stockId, $locationId);

        if ($linkStmt->execute()) {
            return ['success' => true, 'stock_id' => $stockId];
        }
    }

    return ['success' => false, 'message' => $conn->error];
}
function getStockGroupedByMedicine($conn, $name)
{
    $sql = "
        SELECT 
            m.id AS medicine_id,
            m.name AS medicine_name,
            m.dosage,
            l.village,
            l.district,
            l.governorate,
            l.address,
            u.name AS provider_name
        FROM medicines m
        JOIN stock s ON m.id = s.medicine_id
        JOIN stock_locations sl ON s.id = sl.stock_id
        JOIN locations l ON sl.location_id = l.id
        JOIN users u ON s.user_id = u.id
        WHERE m.name LIKE ?
    ";

    $stmt = $conn->prepare($sql);
    $search = "%$name%";
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $grouped = [];

    while ($row = $result->fetch_assoc()) {
        $key = $row['medicine_name'] . ' ' . $row['dosage'];

        if (!isset($grouped[$key])) {
            $grouped[$key] = [
                'name' => $row['medicine_name'],
                'dosage' => $row['dosage'],
                'locations' => []
            ];
        }

        $grouped[$key]['locations'][] = [
            'name' => $row['provider_name'],
            'village' => $row['village'],
            'district' => $row['district'],
            'governorate' => $row['governorate'],
            'address' => $row['address']
        ];
    }

    return array_values($grouped);
}
?>