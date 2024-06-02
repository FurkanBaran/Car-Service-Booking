<?php
include '../db_connection.php'; /// Veritabanını sayfaya dahil etme

header('Content-Type: application/json'); // AJAX yanıtları için içerik türünü JSON olarak ayarla

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo json_encode(['error' => 'Yetkisiz erişim.']);
    exit;
}

// Verilerin mevcutluğunu kotrol et
if (isset($_POST['service'], $_POST['date'], $_POST['time'])) {
    $serviceId = $_POST['service'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $customerId = $_SESSION['user_id'];

    // Saat ve tarih formatını valide et
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !preg_match('/^[0-2][0-9]:[0-5][0-9]$/', $time)) {
        echo json_encode(['error' => 'Geçersiz tarih veya saat formatı.']);
        exit;
    }

    // Seçilen tarih ve saatin geçmiş olup olmadığını kontrol et
    $currentTime = new DateTime(); // Geçerli tarih ve saati al
    $selectedDateTime = new DateTime($date . ' ' . $time); // Verilerle tarih ve saati birleştir
    if ($selectedDateTime < $currentTime) {
        echo json_encode(['error' => 'Geçmiş bir tarih veya saat seçemezsiniz.']);
        exit;
    }

    // Seçilen zaman aralığının zaten rezerve edilip edilmediğini ve onaylanıp onaylanmadığını kontrol et

    $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE service_id = ? AND date = ? AND time = ? AND status = 'confirmed'");
    $stmt->execute([$serviceId, $date, $time]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo json_encode(['error' => 'Bu tarih ve saat zaten rezerve edilmiş. Lütfen başka bir zaman seçin.']);
        exit;
    }

    // Yeni randevuyu ekle

    $stmt = $conn->prepare("INSERT INTO appointments (service_id, customer_id, date, time, status) VALUES (?, ?, ?, ?, 'pending')");
    $success = $stmt->execute([$serviceId, $customerId, $date, $time]);

    if ($success) {
        echo json_encode(['success' => 'Randevu başarıyla oluşturuldu.']);
    } else {
        echo json_encode(['error' => 'Randevu oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.']);
    }
} else {
    echo json_encode(['error' => 'Eksik veri. Lütfen tüm alanları doldurun.']);
}
?>
 