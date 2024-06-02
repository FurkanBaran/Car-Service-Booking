<?php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../db_connection.php'; 

// servislerin veritabanından alınması
if (isset($_POST['district'], $_POST['city'], $_POST['vehicle_type'])) {
    $district = $_POST['district'];
    $city = $_POST['city'];
    $vehicleType = $_POST['vehicle_type'];

    $sql = "SELECT s.id, s.name, s.phone, s.address FROM services s
            JOIN service_vehicle_types svt ON s.id = svt.service_id
            WHERE s.city = ? AND s.district = ? AND svt.vehicle_type_id = ?";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([$city, $district, $vehicleType]);
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // servislerin JSON formatında döndürülmesi
        $options = [];
        foreach ($services as $service) {
            $options[] = [
                'name' => $service['name'],
                'address' => $service['address'],
                'phone' => $service['phone'],
                'id' => $service['id']
            ];
        }
        echo json_encode(['services' => $options]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}
?>