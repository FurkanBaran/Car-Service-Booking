<?php
include '../../db_connection.php'; // veritabanı bağlantısını dahil et

header('Content-Type: application/json'); // json yanıtı olduğunu belirt

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo json_encode(['error' => 'Kullanıcı Girişi yapınız.']);
    exit;
}

//  Tüm gerekli bilgiler gönderilmiş mi kontrol et
if (isset($_POST['service'], $_POST['date'], $_POST['time'], $_POST['vehicleType'])) {
    $serviceId = $_POST['service'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $vehicleTypeId = $_POST['vehicleType'];

    $customerId = $_SESSION['user_id'];

    // seçilen tarih ve saat formatı doğru mu kontrol et
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !preg_match('/^[0-2][0-9]:[0-5][0-9]$/', $time)) {
        echo json_encode(['error' => 'Geçersiz tarih veya saat formatı.']);
        exit;
    }

    // seçilen tarih ve saat geçmiş bir tarih veya saat mi kontrol et
    $currentTime = new DateTime(); // şu anki tarih ve saat
    $selectedDateTime = new DateTime($date . ' ' . $time); // tarihi ve saati birleştir
    if ($selectedDateTime < $currentTime) {
        echo json_encode(['error' => 'Geçmiş bir tarih veya saat seçemezsiniz.']);
        exit;
    }

    // seçilen tarih ve saat için başka bir randevu var mı kontrol et
    $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE service_id = ? AND date = ? AND time = ? AND status = 'Onaylandı'");
    $stmt->execute([$serviceId, $date, $time]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo json_encode(['error' => 'Bu tarih ve saat zaten rezerve edilmiş. Lütfen başka bir tarih ve saat seçiniz.']);
        exit;
    }

    // Yeni randevuyu veritabanına yükle
    $stmt = $conn->prepare("INSERT INTO appointments (service_id, customer_id, vehicle_type_id , date, time, status) VALUES (?, ?,?, ?, ?, 'Beklemede')");
    $success = $stmt->execute([$serviceId, $customerId, $vehicleTypeId , $date, $time]);

    if ($success) {
        echo json_encode(['success' =>  ' Randevu başarıyla oluşturuldu.']);
    } else {
        echo json_encode(['error' => ' Randevu oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.']);
    }
} else {
    echo json_encode(['error' => ' Eksik bilgi gönderildi. Lütfen tüm alanları doldurunuz.']);
}
?>
 