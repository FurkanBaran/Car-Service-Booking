<?php
session_start();
include '../db_connection.php'; // Veritabanı bağlantısını dahil et

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'service') {
    header('Location: login.html'); // giriş yapmamışsa giriş sayfasına yönlendir
    exit();
}
function translateDayToTurkish($dayOfWeek) { // günleri türkçeye çevir
    switch ($dayOfWeek) {
        case 'Monday': return 'Pazartesi';
        case 'Tuesday': return 'Salı';
        case 'Wednesday': return 'Çarşamba';
        case 'Thursday': return 'Perşembe';
        case 'Friday': return 'Cuma';
        case 'Saturday': return 'Cumartesi';
        case 'Sunday': return 'Pazar';
        default: return null;
    }
}

$service_id = $_SESSION['user_id'];

// ilgili servis merkezinin günlere göre çalışma saatlerini getir
$daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$existingHours = [];

foreach ($daysOfWeek as $day) {
    $stmt = $conn->prepare("SELECT start_hour, end_hour FROM working_hours WHERE service_id = ? AND day = ?");
    $stmt->execute([$service_id, $day]);
    $hours = $stmt->fetch(PDO::FETCH_ASSOC);
    $existingHours[$day] = $hours ? $hours : ['start_hour' => '', 'end_hour' => ''];
}
// Veritabanından servis merkezi adını ve randevuları al
$stmt = $conn->prepare("SELECT name FROM services WHERE id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch();


// tüm araç tiplerini getir
$vehicleTypesStmt = $conn->prepare("SELECT id, type FROM vehicle_types ORDER BY type ASC");
$vehicleTypesStmt->execute();
$vehicleTypes = $vehicleTypesStmt->fetchAll(PDO::FETCH_ASSOC);

// ilgili servis merkezinin seçili araç tiplerini getir
$selectedTypesStmt = $conn->prepare("SELECT vehicle_type_id FROM service_vehicle_types WHERE service_id = ?");
$selectedTypesStmt->execute([$service_id]);
$selectedVehicleTypes = $selectedTypesStmt->fetchAll(PDO::FETCH_COLUMN);




// Form gönderildiğinde çalışacak kod
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_hours'])) {
    $selectedTypes = $_POST['vehicleTypes'] ?? [];

    if (count($selectedTypes) === 0) {
        $err= "<p>En az bir araç tipi seçilmeli. </p>";
    }
    else{
        foreach ($daysOfWeek as $day) {
            $start_hour = $_POST[$day . '_start'];
            $end_hour = $_POST[$day . '_end'];

            //  Eğer saatler boş değilse, güncelleme yap
            if ($existingHours[$day]['start_hour'] != '') {
                // Eğer saatler boş değilse, güncelleme yap
                $update = $conn->prepare("UPDATE working_hours SET start_hour = ?, end_hour = ? WHERE service_id = ? AND day = ?");
                $update->execute([$start_hour, $end_hour, $service_id, $day]);
            } else {
                // Eğer saatler boşsa, yeni saatler ekle
                $insert = $conn->prepare("INSERT INTO working_hours (service_id, day, start_hour, end_hour) VALUES (?, ?, ?, ?)");
                $insert->execute([$service_id, $day, $start_hour, $end_hour]);
            }
        }

        // Eski araç tiplerini sil
        $clearTypes = $conn->prepare("DELETE FROM service_vehicle_types WHERE service_id = ?");
        $clearTypes->execute([$service_id]);

        //  Yeni araç tiplerini ekle
        foreach ($selectedTypes as $typeId) {
            $insertType = $conn->prepare("INSERT INTO service_vehicle_types (service_id, vehicle_type_id) VALUES (?, ?)");
            $insertType->execute([$service_id, $typeId]);
        }

        $message= "<p> Servis bilgileri başarıyla güncellendi! </p>";
    }
}


?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çalışma Saatlerini Güncelle </title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
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
    <div class="container">
        <br>
        <h1 class="mb-4">Servis Hizmet Bilgilerini Güncelle</h1>
        <div id="messageArea" class="alert alert-danger" style=" margin-bottom: 10px;"><?php if (!empty($err)) echo $err ; if (!empty($message)) echo $message ?> 00.00-00.00 aralığını işaretlerseniz, o gün çalışmadığınızı belirtmiş olursunuz!</div> 
        <form action="" method="POST" onsubmit="return validateForm()">
            <?php foreach ($daysOfWeek as $day): 
                $day_tr = translateDayToTurkish($day);
            ?>
                <div class="form-row">
                    <div class="col-md-6">
                        <fieldset>
                            <legend><?= $day_tr ?></legend>
                            <div class="form-group">
                                <label for="<?= $day ?>_start">Başlangıç Saati:</label>
                                <input type="time" class="form-control" id="<?= $day ?>_start" name="<?= $day ?>_start" value="<?= $existingHours[$day]['start_hour'] ?>" required>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset>
                            <legend>&nbsp;</legend>
                            <div class="form-group">
                                <label for="<?= $day ?>_end">Bitiş Saati:</label>
                                <input type="time" class="form-control" id="<?= $day ?>_end" name="<?= $day ?>_end" value="<?= $existingHours[$day]['end_hour'] ?>" required>
                            </div>
                        </fieldset>
                    </div>
                </div>
                    <hr>
            <?php endforeach; ?>
            <div class="form-group">
                <fieldset>
                    <legend>Araç Tipleri:</legend>
                    <?php foreach ($vehicleTypes as $type): ?>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="type<?= $type['id'] ?>" name="vehicleTypes[]" value="<?= $type['id'] ?>" <?= in_array($type['id'], $selectedVehicleTypes) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="type<?= $type['id'] ?>"><?= htmlspecialchars($type['type']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </fieldset>
            </div>
            <button type="submit" name="save_hours" class="btn btn-primary">Bilgileri Kaydet</button>
        </form>
    </div>
</body>
<script>
    function validateForm() { // formu kontrol et
        const checkboxes = document.querySelectorAll('input[name="vehicleTypes[]"]');
        const isAnyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

        if (!isAnyChecked) {
            document.getElementById('messageArea').innerHTML = 'En az bir araç tipi seçilmeli.';
            document.getElementById('messageArea').style.display = 'block';
            return false; 
        } else {
            document.getElementById('messageArea').style.display = 'none';
            return true;
        }
    }
</script>
</html>
