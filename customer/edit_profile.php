<?php
session_start();
include '../db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: ../login.php');
    exit();
}
$message = '';
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $phone = $_POST['phone'];
    if (empty($name) || empty($city) || empty($district) || empty($phone)) {
         $message = 'Lütfen tüm alanları doldurun.';
        echo 'Lütfen tüm alanları doldurun.';
        exit();
    }else {
        $message="password";
        $stmt = $conn->prepare("UPDATE customers SET name = ?, city = ?, district = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $city, $district, $phone, $user_id]);
    }
    


    header('Location: edit_profile.php');
    exit();
}

$stmt = $conn->prepare("SELECT c.name, c.city, c.district, c.phone FROM customers c WHERE c.id = ?");
$stmt->execute([$user_id]);
$customer = $stmt->fetch();
$customer_name = $customer['name'];
$city = $customer['city'];
$district = $customer['district'];
$phone = $customer['phone'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
                <li><a href="customer_dashboard.php">Müşteri Paneli</a></li>
                <li><a href="edit_profile.php">Profili Düzenle</a></li>
                <li><a href="../logout.php">Çıkış</a></li>
            </ul>
        </nav>
    </header>
    <div class="content">
        <div class="container mt-5"> 
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <h2 class="mb-3">Profil Düzenle</h2>
                    <div id="messageArea" class="alert alert-danger" style="display: none;"></div>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name">İsim</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($customer_name) ?>">
                        </div>
                        <div class="form-group">
                            <label for="city">Şehir</label>
                            <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($city) ?>">
                        </div>
                        <div class="form-group">
                            <label for="district">İlçe</label>
                            <input type="text" class="form-control" id="district" name="district" value="<?= htmlspecialchars($district) ?>">
                        </div>
                        <div class="form-group">
                            <label for="phone">Telefon</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('form').submit(function(e) {
                e.preventDefault();
                var name = $('#name').val();
                var city = $('#city').val();
                var district = $('#district').val();
                var phone = $('#phone').val();
                var password = $('#password').val();

                // AJAX ile verileri sunucuya gönder
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: {
                        name: name,
                        city: city,
                        district: district,
                        phone: phone,
                        password: password
                    },
                    success: function(response) {
                        // Başarılı bir şekilde güncellendiğinde kullanıcıyı yönlendir
                        $('#messageArea').text('Profil başarıyla güncellendi.').show();
                        
                    },
                    error: function(xhr, status, error) {
                        // Hata durumunda kullanıcıya bir mesaj göster
                        $('#messageArea').text('Bir hata oluştu. Lütfen tekrar deneyin.').show();
                    }
                });
            });
        });
    </script>
</body>
</html>