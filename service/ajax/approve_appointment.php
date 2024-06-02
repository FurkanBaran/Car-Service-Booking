

<?php
include '../../db_connection.php'; // Veri tabanını dahil et

if (isset($_POST['id']) && isset($_POST['action'])) {
    $id = $_POST['id'];
    $action = $_POST['action'];


    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Onaylandı' WHERE id = ?");
    } elseif ($action === 'decline') {
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Reddedildi' WHERE id = ?");
    } else {
        echo json_encode(['error' => 'Invalid action']);
        exit;
    }

    try {
        $stmt->execute([$id]);
        echo json_encode(['success' => 'Randevu başarıyla güncellendi']);
    } catch (\Throwable $th) {
        echo json_encode(['error' => 'Veritabanı hatası ' . $th->getMessage()]);
    }
}
else
{
    echo json_encode(['error' => 'Geçersiz istek']);
}
?>