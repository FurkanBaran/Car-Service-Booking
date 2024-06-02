<?php
include '../../db_connection.php';  // veritabanı bağlantısını dahil et

// randevu iptal isteği gönderildiğinde çalışacak kod
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $appointmentId = $_POST['id'];

    // işlem başlat
    $conn->beginTransaction();

    try {
        // randevunun durumunu kontrol et
        $stmt = $conn->prepare("SELECT status FROM appointments WHERE id = ?");
        $stmt->execute([$appointmentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['status'] === 'Beklemede') {
            // eğer randevu iptal edilebilirse iptal et
            $updateStmt = $conn->prepare("UPDATE appointments SET status = 'İptal edildi' WHERE id = ?");
            $updateStmt->execute([$appointmentId]);

            // işlemi tamamla
            $conn->commit();

            echo json_encode(['success' => true, 'message' => 'Randevu başarıyla iptal edildi.']);
        } else {
            // eğer randevu iptal edilemezse işlemi geri al
            $conn->rollBack();
            echo json_encode(['success' => false, 'message' => 'Randevu iptal edilemez. Lütfen tekrar deneyin.']);
        }
    } catch (PDOException $e) {
        // eğer bir hata olursa işlemi geri al
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek. Lütfen tekrar deneyin.']);
}
?>
