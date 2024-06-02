<?php
session_start();
include '../db_connection.php'; // Veritabanı bağlantısını dahil et 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../login.php'); // Kimlik doğrulanmadıysa giriş sayfasına yönlendir
    exit();
}
$user_id = $_SESSION['user_id'];

// Veritabanından araç tiplerini al
$stmt = $conn->prepare("SELECT id, type FROM vehicle_types ORDER BY type ASC");
$stmt->execute();
$vehicleType = $stmt->fetchAll();


// Müşteri adını, şehrini ve randevularını veritabanından al
$stmt = $conn->prepare("SELECT c.name, c.city, c.district FROM customers c WHERE c.id = ?");
$stmt->execute([$user_id]);
$customer = $stmt->fetch();
$customer_name = $customer['name'];
$city = $customer['city'];


// Randevu hizmet adını, tarihini, saatini, araç tipini ve durumunu al
$stmt = $conn->prepare("SELECT a.date, a.time, a.status, a.id, s.name  as service_name, vt.type as vehicle_type FROM appointments a
                        JOIN services s ON a.service_id = s.id
                        JOIN vehicle_types vt ON a.vehicle_type_id = vt.id
                        WHERE a.customer_id = ? ORDER BY a.date, a.time");

$stmt->execute([$user_id]);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> <!-- jQuery kütüphanesini dahil et -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Paneli- <?= htmlspecialchars($customer['name']) ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Hoşgeldiniz, <?= htmlspecialchars($customer['name']) ?></h1>
        <nav>
            <ul>
                <li><a href="edit_profile.php">Profili Düzenle</a></li>
                <li><a href="../logout.php">Çıkış</a></li>
            </ul>
        </nav>
    </header>
    <div class="content">
        <div class="container mt-5"> 
            <h2 >Randevu Al</h2>
            <hr>
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div id="messageArea" class="alert alert-danger" style="display: none;"></div> <!-- uyarı mesajı -->

                    <form id="appointmentForm">
                        <div class="form-group">
                            <label for="city">Şehir:</label>
                            <select id="city" name="city" class="form-control">
                                <option value="">Şehir Seçiniz</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="district">İlçe:</label>
                            <select id="district" name="district" class="form-control" disabled>
                                <option value="">İlçe Seçiniz</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="vehicleType">Desteklenen Araç Tipleri:</label>
                            <select id="vehicleType" name="vehicleType" class="form-control">
                                <option value="">Araç Tipi Seçiniz</option>
                                <?php foreach ($vehicleType as $type): ?>
                                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['type']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="service">Servis:</label>
                            <!-- servis tablosu -->
                            <table id="serviceTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>İsim</th>
                                        <th>Adres</th>
                                        <th>Telefon</th>
                                        <th>Seç</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div class="form-group">
                            <label for="date">Tarih:</label>
                            <input type="date" id="date" name="date" class="form-control" disabled>
                        </div>

                        <div class="form-group">
                            <label for="time">Saat:</label>
                            <select id="time" name="time" class="form-control" disabled>
                                <option value="">Saat</option>
                            </select>
                        </div>
                        <button type="button" id="bookButton" class="btn btn-primary" disabled>Randevu İsteği Gönder</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="container mt-5"><!-- Var olan randevuları görüntülemek için bölüm -->
            <h2>Randevularınız</h2>
            <hr>
            <?php if (count($appointments) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Servis Adı</th>
                        <th>Tarih</th>
                        <th>Saat</th>
                        <th>Araç Tipi</th>
                        <th>Durum</th>
                        <th>İptal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?= htmlspecialchars($appointment['service_name']) ?></td>
                            <td><?= htmlspecialchars($appointment['date']) ?></td>
                            <td><?= htmlspecialchars($appointment['time']) ?></td>
                            <td><?= htmlspecialchars($appointment['vehicle_type']) ?></td>
                            <td><?= htmlspecialchars($appointment['status']) ?></td>
                            <td>
                                <?php if ($appointment['status'] === 'Beklemede'): ?>
                                    <button onclick="cancelAppointment(<?= htmlspecialchars($appointment['id']) ?>)">İptal</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>Randevu bulunamadı. </p>
            <?php endif; ?>
        </div>
    </div>


    <script>
        function showMessage(message, type) {
            var messageArea = $('#messageArea');
            messageArea.text(message).css('display', 'block');
            
            // Mesajın türüne göre stilleri güncelle
            if (type === 'error') {
                messageArea.css('color', '#dc3545').css('background-color', '#f8d7da');
            } else if (type === 'success') {
                messageArea.css('color', '#155724').css('background-color', '#d4edda');
            } else {
                messageArea.css('color', '#856404').css('background-color', '#fff3cd');
            }
        }


        /*
        * Aşağıdaki kodlar, form gönderimini ve şehirleri, ilçeleri, servisleri ve saatleri almak için AJAX isteklerini yönetir.
        */
        $(document).ready(function() {
            loadCities();


            $('#city').change(function() {
                var city = $(this).val();
                if (city) {
                    loadDistricts(city);
                } else {
                    $('#district').empty().append('<option value=""> İlçe Seçiniz</option>').prop('disabled', true);
                    $('#service').empty().append('<option value="">Servis Seçiniz</option>').prop('disabled', true);
                }
            });
            


            $('#vehicleType').change(function() {
                var city = $('#city').val();
                var district = $('#district').val();
                if (district) {
                    loadServices(city, district, $(this).val());
                } else {
                    $('#serviceTable').empty().append('<option value="">Servis Seçiniz</option>').prop('disabled', true);
                }
            });

            $('#serviceTable').change(function() {
                $('#date').prop('disabled', false);
            });


            $('#date').change(function() {
                var serviceId = $('input[name="service"]:checked').val();
                var date = $(this).val();
                if (date) {
                    loadTimes(serviceId, date);
                } else {
                    $('#time').empty().append('<option value=""> Saat Seçiniz</option>').prop('disabled', true);
                }
            });

            $('#time').change(function() {
                var time = $(this).val();
                if (time) {
                    $('#bookButton').prop('disabled', false);
                } else {
                    $('#bookButton').prop('disabled', true);
                }
            });

            $('#bookButton').click(function() { // Form gönderildiğinde
                var formData = $('#appointmentForm').serialize();
                $.ajax({
                    url: 'ajax/book_appointment.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                        // Eğer yanıtta 'success' anahtarı varsa
                        showMessage(response.success, 'success'); // Başarı mesajını göster
                        
                        } else if (response.error) {
                            // Eğer yanıtta 'error' anahtarı varsa
                            showMessage(response.error, 'error'); // Hata mesajını göster
                        }                 
                    },
                    error: function(xhr, status, error) {
                        showMessage('Sunucu iletişiminde hata:' + error, 'error');
                        console.log(xhr, status, error);

                    }
                });
            });
        });



        // Şehirleri yükleme fonksiyonu
        function loadCities() {
            $.ajax({
                url: 'ajax/fetch_cities.php', // Şehirleri HTML seçenekleri olarak döndüren php dosyası
                method: 'GET',  //  Veri almak için GET kullan
                dataType: 'json', // JSON yanıtı bekleniyor
                success: function(data) {
                    var citySelect = $('#city');
                    if (data.cities) { // Yanıtta şehir verileri var mı diye kontrol et
                        citySelect.html(data.cities); // Yanıttan gelen HTML'yi doğrudan kullan

                        citySelect.prop('disabled', false); // Şehir seçimini etkinleştir
                        // Eğer müşterinin şehri varsa, şehri seçili hale getir
                         $('#city').val('<?= $city ?>').trigger('change');
                    } else {
                        citySelect.html('<option value="">Şehir Bulunamadı</option>'); // Yanıtta şehir verisi yoksa hata mesajı göster
                        citySelect.prop('disabled', true); //  Şehir seçimini devre dışı bırak
                    }
                },
                error: function() { // Eğer bir hata olursa
                    showMessage('Şehirler yüklenirken bir hata oluştu.', 'error');  // Hata mesajı göster
                    $('#city').prop('disabled', true).html('<option value="">Şehir Bulunamadı</option>');
                }
            });
        
        }


        // İlçeleri yükleme fonksiyonu
        function loadDistricts(city) {
            $.ajax({
                url: 'ajax/fetch_districts.php',
                method: 'POST',
                data: { city: city },
                dataType: 'json',
                success: function(data) {
                    var districtSelect = $('#district');
                    $('#district').html(data.districts); 

                    if(data.districts.length > 0) {
                        districtSelect.prop('disabled', false);

                    } else {
                        alert('Seçilen şehir için ilçe bulunamadı. Lütfen başka bir şehir seçin.');
                        districtSelect.prop('disabled', true);
                    }
                },
                error: function() {
                    showMessage('İlçeler yüklenirken bir hata oluştu.', 'error');
                }
            });
        }
        // Servisleri yükleme fonksiyonu
        function loadServices(city, district, vehicleType) {
            $.ajax({
                url: 'ajax/fetch_services.php',
                method: 'POST',
                data: { city: city, district: district, vehicle_type: vehicleType },
                dataType: 'json',
                success: function(data) {
                    // Verileri html tabloya yerleştir
                    const tableBody = document.getElementById('serviceTable').getElementsByTagName('tbody')[0];
                    tableBody.innerHTML = '';
                    data.services.forEach(service => {
                        const row = tableBody.insertRow();
                        row.insertCell(0).textContent = service.name; // İsim
                        row.insertCell(1).textContent = service.address; // Adres
                        row.insertCell(2).textContent = service.phone; // Telefon Numarası
                        const selectCell = row.insertCell(3);
                        const radioButton = document.createElement('input');
                        radioButton.type = 'radio';
                        radioButton.name = 'service';
                        radioButton.value = service.id;
                        selectCell.appendChild(radioButton);
                    });
                },
                error: function() {
                    showMessage('Servisler yüklenirken bir hata oluştu.', 'error');
                }
            });
        }


        // Saatleri yükleme fonksiyonu
        function loadTimes(serviceId, date) {
            $.ajax({
                url: 'ajax/fetch_times.php', 
                method: 'POST',
                data: { serviceId: serviceId, date: date },
                dataType: 'json',
                success: function(data) {
                    var timeSelect = $('#time');
                    if(data.error) {
                        alert(data.error);
                        timeSelect.empty().append('<option value=""> Saat Seçiniz</option>').prop('disabled', true);
                    } else {
                        $('#time').html(data.times);
                        
                        timeSelect.prop('disabled', false);
                    }
                },
                error: function() {
                    showMessage()
                }
            });
        }

        // Randevuyu iptal etme fonksiyonu
        function cancelAppointment(appointmentId) {
            if (confirm('Bu randevuyu iptal etmek istediğinize emin misiniz?')) {
                fetch('ajax/cancel_app.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + appointmentId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Randevu başarıyla iptal edildi.');
                        location.reload();
                    } else {
                        alert(' Bir hata oluştu. Lütfen tekrar deneyin.');
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                });
            }
        }



    </script>
    
</body>
</html>
 