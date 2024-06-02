<?php
include '../../db_connection.php'; // veritabanı bağlantısını dahil et

header('Content-Type: application/json');  // yanıtın JSON olduğunu belirt

if (isset($_POST['serviceId']) && isset($_POST['date'])) {
    $serviceId = $_POST['serviceId'];
    $selectedDate = $_POST['date'];

    //  Geçmiş bir tarih seçilmişse hata döndür
    if (strtotime($selectedDate) < strtotime('today')) {
        echo json_encode(['error' => 'Geçmiş bir tarih seçemezsiniz. Lütfen bugünden ileri bir tarih seçiniz.']);
        exit; // Hata döndürdüğümüz için işlemi sonlandır
    }

    $dayOfWeek = date('l', strtotime($selectedDate));  // Seçilen tarihin haftanın hangi günü olduğunu al

    $stmt = $conn->prepare("SELECT start_hour, end_hour FROM working_hours WHERE service_id = ? AND day = ?");
    $stmt->execute([$serviceId, $dayOfWeek]);
    $times = $stmt->fetchAll();

    $options = "<option value=''>Saat seçiniz </option>";
    $nonOperational = true;  //  Varsayılan olarak servisin bu gün hizmet vermediğini belirt

    foreach ($times as $time) {
        $start = new DateTime($time['start_hour']);
        $end = new DateTime($time['end_hour']);

        if ($start->format('H:i') == '00:00' && $end->format('H:i') == '00:00') {
            continue;  // Gece yarısından gece yarısına kadar hizmet verilmiyorsa bu gün hizmet verilmiyor demektir
        }

        $nonOperational = false;  // Servis bu gün hizmet veriyor

        // Saat aralıklarını 1 saatlik olarak oluştur
        while ($start < $end) {
            $timeString = $start->format('H:i');
            $options .= "<option value='" . $timeString . "'>" . $timeString . "</option>";
            $start->modify('+1 hour'); // Saati 1 saat artır
        }
    }

    if ($nonOperational) {
        echo json_encode(['error' => 'Servis bu gün hizmet vermemektedir. Lütfen başka bir gün seçiniz.']);
    } else {
        echo json_encode(['times' => $options]);  // Saat seçeneklerini JSON olarak döndür
    }
}
?>
