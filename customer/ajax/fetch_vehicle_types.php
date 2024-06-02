<?php
include '../../db_connection.php'; // Veritabanını sayfaya dahil etme

if (isset($_POST['district']) && isset($_POST['city']) && isset($_POST['vehicle_type'] )) {
    $city = $_POST['city'] ?? '';
    $district = $_POST['district'] ?? '';
    
    $sql = "SELECT DISTINCT vt.id, vt.type FROM vehicle_types vt
            JOIN service_vehicle_types svt ON vt.id = svt.vehicle_type_id
            JOIN services s ON svt.service_id = s.id
            WHERE s.city = 'Kartal' AND s.district = 'sada'";


    $stmt = $conn->prepare($sql);
    $stmt->execute([$city, $district]);
    $services = $stmt->fetchAll();

    $options = "<option value=''>Araç tipi seçiniz</option>";
    foreach ($services as $service) {
        $options .= "<option value='" . htmlspecialchars($service['id']) . "'>" . htmlspecialchars($service['name']) . "</option>";
    }
    echo json_encode(['services' => $options]);
}


