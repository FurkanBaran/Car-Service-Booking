<?php
session_start();
include '../db_connection.php'; // Veritabanı bağlantısını dahil et

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'service') {
    header('Location: login.php'); // Kimlik doğrulanmamışsa giriş sayfasına yönlendir
    exit();
}

$user_id = $_SESSION['user_id'];

// Veritabanından servis merkezi adını ve randevuları al
$stmt = $conn->prepare("SELECT name FROM services WHERE id = ?");
$stmt->execute([$user_id]);
$service = $stmt->fetch();

// servis merkezi için randevuları al, Müşteri adı, telefon numarası, araç tipi verilerini al
$stmt = $conn->prepare("SELECT c.name, c.phone, vt.type AS vehicle_type, a.date, a.time, a.status, a.id, s.name AS service_name
                        FROM appointments a
                        JOIN customers c ON a.customer_id = c.id
                        JOIN services s ON a.service_id = s.id
                        JOIN vehicle_types vt ON a.vehicle_type_id = vt.id
                        WHERE a.service_id = ?");
$stmt->execute([$user_id]);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servis Merkezi Paneli - <?= htmlspecialchars($service['name']) ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> <!-- jQuery kütüphanesini dahil et -->
</head>
<body>
    <header>

        <h1>Hoşgeldiniz, <?= htmlspecialchars($service['name']) ?></h1>
        <nav>
            <ul>
            <li><a href="service_dashboard.php">Randevu Paneli</a></li>
            <li><a href="edit_profile.php">Hizmet Seçenekleri</a></li>
            <li><a href="../logout.php">Çıkış</a></li>
            </ul>
        </nav>
    </header>
    <section>
        <div class="container mt-5"><!-- Bu servise ait randevuları görüntülemek için bölüm -->
        <h2>Randevularınız</h2>
            <?php if (count($appointments) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Müşteri Adı</th>
                            <th>Telefon Numarası</th>
                            <th>Araç Tipi</th>
                            <th>Tarih</th>
                            <th>Saat</th>
                            <th>Durum</th>
                            <th>Güncelle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?= htmlspecialchars($appointment['name']) ?></td>
                                <td><?= htmlspecialchars($appointment['phone']) ?></td>
                                <td><?= htmlspecialchars($appointment['vehicle_type']) ?></td>
                                <td><?= htmlspecialchars($appointment['date']) ?></td>
                                <td><?= htmlspecialchars($appointment['time']) ?></td>
                                <td><?= htmlspecialchars($appointment['status']) ?></td>
                                <td>
                                    <?php if ($appointment['status'] === 'Beklemede'): ?> <!-- Eğer randevu durumu 'Beklemede' ise -->
                                        <button style="background-color:green;" onclick="approveAppointment(<?= htmlspecialchars($appointment['id']) ?>)">Kabul</button>
                                        <button  style="background-color:red;" onclick="declineAppointment(<?= htmlspecialchars($appointment['id']) ?>)">Red</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Randevu bulunamadı. </p>
            <?php endif; ?>
    </section>
</body>
<script>
    function  approveAppointment(id) {
        $.ajax({
            url: 'ajax/approve_appointment.php',  // Sunucu tarafı işlem dosyası
            type: 'POST',  // HTTP isteği türü
            data: {
                id: id,  // id değişkeninin değeri
                action: 'approve'  // 'kabul' işlemi
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    alert(data.success);
                    location.reload();  // Sayfayı yenile
                } else {
                    alert(data.error);
                }
            },
            error: function(xhr, status, error) {
                alert('Hata: ' + error);
            }
        });

    }

    function declineAppointment(id) {
        $.ajax({
            url: 'ajax/approve_appointment.php',  // Sunucu tarafı işlem dosyası
            type: 'POST',  // HTTP isteği türü
            data: {
                id: id,  // id değişkeninin değeri
                action: 'decline'  // 'red' işlemi
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    alert(data.success);
                    location.reload();  // Sayfayı yenile
                } else {
                    alert(data.error);
                }
            },
            error: function(xhr, status, error) {
                alert('Hata: ' + error);
            }
        });

    }
    
</script>
</html>
