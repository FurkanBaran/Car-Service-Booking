<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pratik Oto Servis Randevu Sistemi</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-image: url('images/bg.jpg');
            background-size: cover;
        }
        .custom-box {
            border-radius: 10px;
            box-shadow: 0px 0px 10px 2px rgba(0,0,0,0.1);
            padding: 20px;
            height: 100%;
        }
        .btn-custom {
            padding: 15px 30px;
            margin: 10px 0;
            font-size: 20px;
            cursor: pointer;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title">Pratik Oto Servis Randevu Sistemi</h1>
        <div class="row justify-content-center align-items-stretch">
            <div class="col-md-6">
                <div class="custom-box">
                    <h2>Giriş Yap</h2>
                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label for="role">Giriş Yap As:</label><br>
                            <select id="role" name="role" class="form-control" required>
                                <option value="customer">Müşteri</option>
                                <option value="service">Oto Servis</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="username">Kullanıcı Adı:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Şifre:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button class="btn btn-primary btn-custom">Giriş</button>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="custom-box">
                    <h2>Kayıt Ol</h2>
                    <button class="btn btn-success btn-custom" onclick="location.href='customer/signup.php'">Müşteri Kaydı</button>
                    <button class="btn btn-info btn-custom" onclick="location.href='service/signup.php'">Oto Servis Kaydı</button>
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
