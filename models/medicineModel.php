<?php
function getAllMedicines($conn)
{
    $query = "SELECT * FROM medicines";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function getMedicineById($conn, $id)
{
    $query = "SELECT * FROM medicines WHERE id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    return $result;
}

function createMedicine($conn, $name, $category, $dosage, $description)
{
    $query = "INSERT INTO medicines (name, category, dosage, description) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $name, $category, $dosage, $description);

    return $stmt->execute();
}

function updateMedicine($conn, $id, $name, $category, $dosage, $description)
{
    $query = "UPDATE medicines SET name = ?, category = ?, dosage = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $name, $category, $dosage, $description, $id);
    $stmt->execute();
    return $stmt;
}


function deleteMedicine($conn, $id)
{
    $query = "DELETE FROM medicines WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt;
}

function searchMedicinesByName($conn, $name) {
    $stmt = $conn->prepare("SELECT * FROM medicines WHERE name LIKE ?");
    $likeName = "%" . $name . "%";
    $stmt->bind_param("s", $likeName);
    $stmt->execute();
    $result = $stmt->get_result();

    $medicines = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $medicines[] = $row;
        }
    }

    return $medicines;
}

function searchMedicineByNameAndLocation($conn, $name, $governorate, $district) {
    $sql = "
        SELECT m.*, s.quantity, s.expiration_date, s.price, 
               l.governorate, l.district, l.village, l.address, 
               u.name as provider_name, u.phone
        FROM medicines m
        JOIN stock s ON m.id = s.medicine_id
        JOIN stock_locations sl ON s.id = sl.stock_id
        JOIN locations l ON sl.location_id = l.id
        JOIN users u ON u.id = s.user_id
        WHERE m.name LIKE ?
          AND l.governorate LIKE ?
          AND l.district LIKE ?
    ";

    $stmt = $conn->prepare($sql);
    $likeName = "%$name%";
    $likeGov = "%$governorate%";
    $likeDist = "%$district%";
    $stmt->bind_param("sss", $likeName, $likeGov, $likeDist);
    $stmt->execute();

    $result = $stmt->get_result();
    $medicines = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $medicines[] = $row;
        }
    }

    return $medicines;
}