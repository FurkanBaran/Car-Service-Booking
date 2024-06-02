<?php
include '../../db_connection.php'; // veritabanı bağlantısını dahil et

header('Content-Type: application/json'); // json yanıtı olduğunu belirt

try {
    $stmt = $conn->prepare("SELECT DISTINCT city FROM services ORDER BY city ASC");
    $stmt->execute();
    $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $options = "<option value=''>Şehir Seçiniz </option>";
    foreach ($cities as $city) {
        $options .= "<option value='" . htmlspecialchars($city['city']) . "'>" . htmlspecialchars($city['city']) . "</option>";
    }

    echo json_encode(['cities' => $options]); // şehirleri JSON olarak döndür
} catch (PDOException $e) {
    echo json_encode(['error' => 'Veritabanı hatası: ' . $e->getMessage()]); // hatayı JSON olarak döndür
}
?> 
