<?php
session_start();
include 'db_connection.php'; //  veritabanı bağlantısını dahil et

$message = ""; // mesajı saklamak için değişken

function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = clean_input($_POST['role']);
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);

    // kullanıcı rolüne göre tablo adını belirle
    $table = $role === 'customer' ? 'customers' : 'services';

    // sql sorgusu ile kullanıcıyı veritabanından al
    $stmt = $conn->prepare("SELECT * FROM $table WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Kullanıcı kimlik doğrulaması başarılı, oturum değişkenlerini ayarla
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $role;

        // Kullanıcı rolüne göre panele yönlendir
        $dashboard = $role === 'customer' ? 'customer/customer_dashboard.php' : 'service/service_dashboard.php';
        header("Location: $dashboard");
        exit();
    } else {
        // gçersiz kullanıcı adı veya şifre
        $message = "Geçersiz kullanıcı adı veya şifre.";
    }
}
?>
 
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
  
        body {
            background-image: url("images/bg2.jpg");
            background-size: cover;
            color: white!important;
        }
        .card{
            margin-top: 50px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(6.7px);
            -webkit-backdrop-filter: blur(6.7px);
        }

    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg p-3 mb-5">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Giriş</h2>
                    <?php if (!empty($message)): ?>
                        <p style="color: red;"><?php echo $message; ?></p>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label for="role">Kullanıcı Rolü:</label>
                            <select class="form-control" id="role" name="role">
                                <option value="customer">Müşteri</option>
                                <option value="service">Oto Servis</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="username">Kullanıcı adı:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Şifre:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Giriş yap</button>
                    </form>
                    <hr>
                    <button onclick="window.location.href='customer/signup.php'" class="btn btn-success btn-block">Müşteri Kaydı</button>
                    <button  onclick="window.location.href='service/signup.php'" class="btn btn-info btn-block">Oto Servis Kaydı</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
