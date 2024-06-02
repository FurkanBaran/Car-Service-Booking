<?php
include '../db_connection.php';  // Veritabanı bağlantı betiğini dahil et

$message = "";  // Kullanıcıya mesaj göstermek için

function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Formun gönderilip gönderilmediğini kontrol et
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    $name = clean_input($_POST['name']);
    $city = clean_input($_POST['city']);
    $district = clean_input($_POST['district']);
    $phone = clean_input($_POST['phone']);
    $address = clean_input($_POST['address']);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO services (username, password, name, phone, city, district, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    try {
        $stmt->execute([$username, $hashed_password, $name, $phone, $city, $district, $address]);
        $message = "Servis Merkezi başarıyla kaydedildi!  <a href='../login.php'>Giriş yapmak için buraya tıklayın</a>";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $err = "Kullanıcı adı zaten mevcut!";
        } else {
            $err = "Hata: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oto Servis Kaydı</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
        <h2 class="text-center mb-4">Oto Servis Kaydı</h2>
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php else: ?>
            <?php if (!empty($err)): ?>
            <div id="messageArea" style="color: red; margin-bottom: 10px;"><!-- Mesaj alanı -->
                <div class="alert alert-danger"><?= $err ?></div></div>
            <?php endif; ?>
            <form method="POST" action="signup.php">
                <div class="form-group">
                    <label for="username">Kullanıcı Adı:</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Şifre:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="name">İş Yeri Adı:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="city">Şehir:</label>
                    <input type="text" class="form-control" id="city" name="city" required>
                </div>
                <div class="form-group">
                    <label for="district">İlçe:</label>
                    <input type="text" class="form-control" id="district" name="district" required>
                </div>
                <div class="form-group">
                    <label for="phone">Telefon:</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="address">Adres:</label>
                    <textarea class="form-control" id="address" name="address" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Kaydol</button>
            </form>
        <?php endif; ?>


    </div>
    </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
