<?php
include '../db_connection.php'; // veritabanı bağlantısını dahil et

$message = ""; // mesajı saklamak için değişken

function clean_input($data) { // gelen veriyi temizle
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
// form gönderildiğinde çalışacak kod
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    $name = clean_input($_POST['name']);
    $city = clean_input($_POST['city']);
    $district = clean_input($_POST['district']);
    $phone = clean_input($_POST['phone']);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO customers (username, password, name, city, district, phone) VALUES (?, ?, ?, ?, ?, ?)");
    try {
        $stmt->execute([$username, $hashed_password, $name, $city, $district, $phone]);
        $message = "Müşteri kaydı başarıyla oluşturuldu!. Giriş yapmak için <a href='../login.php'>buraya tıklayın</a>.";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $err = "Kullanıcı zaten mevcut. Lütfen başka bir kullanıcı adı deneyin.";
        } else {
            $err = "Hata: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Kaydı</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>

    <section>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center">Müşteri Kaydı</h2>
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?= $message ?></div>
                <?php else: ?>
                    <?php if (!empty($err)): ?>
                    <div id="messageArea" style="color: red; margin-bottom: 10px;"><!-- Mesaj alanı -->
                        <div class="alert alert-danger"><?= $err ?></div></div>
                    <?php endif; ?>
                    <form action="signup.php" method="POST">
                        <div class="form-group">
                            <label for="username">Kullanıcı Adı:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Şifre:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <label for="name">Ad:</label>
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
                            <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9]{10}" title="On haneli kod" required>
                            <small class="form-text text-muted">Telefon numarası 10 haneli olmalıdır.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Kayıt Ol</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
            </section>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
