<?php
/*
 * Bu dosya oturumu sonlandırır ve kullanıcıyı giriş sayfasına yönlendirir.
 */

session_start(); // Oturumu başlat

session_destroy(); // Oturumu sil
header('Location: login.php'); // Giriş sayfasına yönlendir
exit();


?>