<?php
include '../../db_connection.php'; // veritabanı bağlantısını dahil et

if (isset($_POST['city'])) {
    $city = $_POST['city'];

    $stmt = $conn->prepare("SELECT DISTINCT district FROM services WHERE city = ? ORDER BY district");
    $stmt->execute([$city]);
    $districts = $stmt->fetchAll();

    $options = "<option value=''>İlçe Seçiniz</option>";
    foreach ($districts as $district) {
        $options .= "<option value='" . htmlspecialchars($district['district']) . "'>" . htmlspecialchars($district['district']) . "</option>";
    }
    echo json_encode(['districts' => $options]);
}
?> 
