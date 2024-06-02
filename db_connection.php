<?php
// Veritabanı bağlantı parametreleri
$host = 'localhost';  // veya veritabanı uzak bir sunucuda ise IP adresi
$dbname = 'car_service';    // veritabanının adı
$user = 'root';       // veritabanı kullanıcı adı
$pass = '';           // veritabanı şifresi

try {
    // Bağlantı detaylarıyla yeni bir PDO örneği oluştur
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    
    // PDO hata modunu istisna olarak ayarla
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // karakter seti UTF-8
    $conn->exec("SET NAMES 'utf8'");
    
} catch (PDOException $e) {
    // Bağlantı hatalarını yönet
    die("Veritabanı bağlantısı başarısız oldu: " . $e->getMessage());
}
?>
